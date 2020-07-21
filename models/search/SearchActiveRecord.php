<?php

namespace app\models\search;

use Yii;
use yii\base\Exception;

/**
 * Base class for all search records
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
abstract class SearchActiveRecord extends \yii\elasticsearch\ActiveRecord
{
    // categories selectable for search
    const SEARCH_API = 'api';
    const SEARCH_GUIDE = 'guide';
    const SEARCH_NEWS = 'news';
    const SEARCH_WIKI = 'wiki';
    const SEARCH_EXTENSION = 'extension';


    /**
     * @var array language analyzers for elasticsearch based on
     * https://www.elastic.co/guide/en/elasticsearch/reference/5.6/analysis-lang-analyzer.html
     */
    public static $languages = [
        // lang => analyzer name
        'en' => 'english',
        'de' => 'german',
        'es' => 'spanish',
        'fr' => 'french',
        'he' => 'standard', // 'hebrew' // currently not supported by elasticsearch
        'id' => 'indonesian',
        'it' => 'italian',
        'ja' => 'standard', // 'japanese' // currently not supported by elasticsearch
        'pl' => 'standard', // 'polish' // currently not supported by elasticsearch
        'pt' => 'portuguese',
        'pt-br' => 'portuguese',
        'ro' => 'romanian',
        'ru' => 'russian',
        'sv' => 'swedish',
        'uk' => 'russian', // 'ukrainian', // currently not supported by elasticsearch
        'zh-cn' => 'chinese',
    ];


    public static function index()
    {
        return YII_ENV_DEV ? 'yiiframework-dev' : 'yiiframework';
    }

    /**
     * @inheritdoc
     * @return SearchActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return new SearchActiveQuery(static::class);
    }

    public static function search($queryString, $version = null, $language = null, $type = null)
    {
        $query = static::find();

        if (isset(static::$languages[$language])) {
            $indexes = [static::index() . "-$language"];
            $analyzer = static::$languages[$language];
        } else {
            $language = null;
            $indexes = array_map(function($i) { return static::index() . "-$i"; }, array_keys(static::$languages));
            $analyzer = 'english';
            $query->indicesBoost = [
                static::index() . '-en' => 20,
            ];
        }

        if ($type === null) {
            $types = [
                SearchApiType::TYPE,
                // SearchApiPrimitive::TYPE,
                SearchGuideSection::TYPE,
                SearchWiki::TYPE,
                SearchExtension::TYPE,
                SearchNews::TYPE
            ];
        } elseif (in_array($type, [self::SEARCH_WIKI, self::SEARCH_EXTENSION, self::SEARCH_NEWS], true)) {
            $types = [$type];
        } elseif ($type === self::SEARCH_API) {
            $types = [SearchApiType::TYPE,/* SearchApiPrimitive::TYPE,*/];
        } elseif ($type === self::SEARCH_GUIDE) {
            // TODO add possibility to search for guide subsections: https://github.com/yiisoft-contrib/yiiframework.com/issues/228
            $types = [SearchGuideSection::TYPE];
        } else {
            throw new Exception('Unknown search type given!');
        }

        // add synonyms
        $synonyms = [
            'javascript' => 'Client Script',
            'js' => 'JavaScript',
            'ar' => 'Active Record',
            'i18n' => 'Internationalization',
            'internationalization' => 'i18n',
        ];
        $words = preg_split('~\s+~', ucwords($queryString), PREG_SPLIT_NO_EMPTY);
        foreach($words as $word) {
            if (isset($synonyms[$lword = mb_strtolower($word)])) {
                $queryString .= ' ' . $synonyms[$lword];
            }
        }

        $queryParts = [];

        // exact match on name field, which is a keyword and not analyzed
        // exact match on unanalyzed title
        $queryParts[] = [
            'bool' => [
                'should' => [
                    ['term' => ['name' => $queryString]],
                    ['term' => ['title' => $queryString]],
                    ['match' => ['name' => $queryString]],
                    ['match' => ['name.camel' => $queryString]],
                ],
                'minimum_should_match' => 1,
            ]
        ];


        // Array Helper -> search for ArrayHelper too
        $camelQuery = implode('', $words);
        if (mb_strtolower($queryString) !== mb_strtolower($camelQuery)) {
            Yii::warning('adding additional things: ' . $camelQuery);
            // exact match on name field, which is a keyword and not analyzed
            $queryParts[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['name' => $camelQuery]],
                        ['term' => ['title' => $camelQuery]],
                        ['match' => ['name' => $camelQuery]],
                        ['match' => ['title.stemmed' => $camelQuery]],
                    ],
                    'minimum_should_match' => 1,
                ]
            ];
        }

        // analyzed match on title and content, boost exact matches over typos
        $queryParts[] = [
            'multi_match' => [
                'query' => $queryString,
                'analyzer' => $analyzer,
                'fields' => [
                    'title.stemmed',
                    'content.stemmed',
                ],
                'type' => 'best_fields',
            ],
        ];

        // analyzed match on content, allow fuzzyness, i.e. typos in the words
        $queryParts[] = [
            'multi_match' => [
                'query' => $queryString,
                'analyzer' => $analyzer,
                // https://www.elastic.co/guide/en/elasticsearch/reference/5.6/common-options.html#fuzziness
                'fuzziness' => 'AUTO',
                'fields' => [
                    'title.stemmed',
                    'content.stemmed',
                ],
                'type' => 'best_fields',
            ],
        ];

        $query->from($indexes, $types);
        $q = [
            'bool' => [
                'should' => $queryParts,
                'minimum_should_match' => 1,
            ],
        ];
        if ($version !== null) {
            $q['bool']['filter'] = ['term' => ['version' => $version]];
        } else {
            // in case 1.1 and 2.0 version matches, boost 2.0 to a higher rank
            // https://stackoverflow.com/questions/19123556/boosting-matched-documents-in-elasticsearch-which-have-a-certain-tag
            $q = [
                'function_score' => [
                    'query' => $q,
                    'functions' => [
                        ['filter' => ['term' => ['version' => '2.0']], 'weight' => 4],
                        ['filter' => ['term' => ['version' => '1.1']], 'weight' => 2],
                        // news have no version so they would be ranked lower i.e. equally ranked as version 1.0
                        // make sure they are on the same level as version 2.0
                        ['filter' => ['term' => ['_type' => 'news']], 'weight' => 4],
                    ],
                ]
            ];
        }
        // boost official content over unofficial content
        $q = [
            'function_score' => [
                'query' => $q,
                'functions' => [
                    ['filter' => ['term' => ['_type' => 'news']], 'weight' => 1.5],
                    ['filter' => ['term' => ['_type' => 'api-type']], 'weight' => 1.5],
                    ['filter' => ['term' => ['_type' => 'guide-section']], 'weight' => 1.5],
                ],
            ]
        ];
        $query->query($q);
        $query->highlight([
            'fields' => [
                'name' => ['fragment_size' => 5000, 'number_of_fragments' => 1],
                'title' => ['fragment_size' => 5000, 'number_of_fragments' => 1],
                'title.stemmed' => ['fragment_size' => 5000, 'number_of_fragments' => 1],
                'content' => ['fragment_size' => 100, 'number_of_fragments' => 5],
                'content.stemmed' => ['fragment_size' => 100, 'number_of_fragments' => 5],
            ],
        ]);
        return $query;
    }

    public static function searchAsYouType($queryString, $version = null, $language = null)
    {
        $query = static::find();

        $indexes = [static::index() . '-en'];
        if ($language === null) {
            $indexes = array_map(function($i) { return static::index() . "-$i"; }, array_keys(static::$languages));
            $query->indicesBoost = [
                static::index() . '-en' => 20,
            ];
        } elseif (isset(static::$languages[$language])) {
            $indexes = [static::index() . "-$language"];
        }

        $types = [
            SearchApiType::TYPE,
            // SearchApiPrimitive::TYPE,
            SearchGuideSection::TYPE,
            SearchWiki::TYPE,
            SearchExtension::TYPE,
            SearchNews::TYPE
        ];
        $query->from($indexes, $types);
        // TODO filter by version if possible
        $query->addSuggester('suggest-title', [
            'prefix' => $queryString,
            'completion' => [
                'field' => 'title.suggest',
                // number of suggestions to return
                'size' => 10,
                'fuzzy' => [
                    'fuzziness' => 'AUTO',
                ],
            ],
        ]);
        // if language is specified, do not suggest on name only on title
        // language specific indexes have no name field
        if ($language === null || $language === 'en') {
            $query->addSuggester('suggest-name', [
                'prefix' => $queryString,
                'completion' => [
                    'field' => 'name.suggest',
                    // number of suggestions to return
                    'size' => 10,
                    'fuzzy' => [
                        'fuzziness' => 'AUTO',
                    ],
                ],
            ]);
        }

        return $query;
    }

    public static function instantiate($row)
    {
        switch($row['_type'])
        {
            case SearchApiType::TYPE: return new SearchApiType();
            case SearchApiPrimitive::TYPE: return new SearchApiPrimitive();
            case SearchGuideSection::TYPE: return new SearchGuideSection();
            case SearchExtension::TYPE: return new SearchExtension();
            case SearchNews::TYPE: return new SearchNews();
            case SearchWiki::TYPE: return new SearchWiki();
        }
        return new static;
    }

    public static function deleteAllForVersion($version)
    {
        static::deleteAll(['version' => $version]);
        static::getDb()->createCommand()->flushIndex(static::index());
    }

    abstract public function getTitle();
    abstract public function getDescription();
    abstract public function getUrl();
    abstract public function getType();

    public static function filterHtml($body)
    {
        // filter out code blocks
        $body = preg_replace('~<pre><code>.*?</code></pre>~', '', $body);
        $body = strip_tags($body);
        return $body;
    }

    public static function getTypeName($type)
    {
        return static::getTypes()[$type] ?? '';
    }

    public static function getTypes()
    {
        return [
            self::SEARCH_GUIDE => 'Guide',
            self::SEARCH_API => 'API',
            self::SEARCH_EXTENSION => 'Extensions',
            self::SEARCH_WIKI => 'Wiki',
            self::SEARCH_NEWS => 'News',
        ];
    }
}
