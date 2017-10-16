<?php

namespace app\models\search;


use Yii;
use yii\base\Exception;
use yii\helpers\Inflector;

abstract class SearchActiveRecord extends \yii\elasticsearch\ActiveRecord
{
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
        return new SearchActiveQuery(get_called_class());
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

        $types = [];
        if ($type === null) {
            $types = ['api-type',/* 'api-primitive',*/ 'guide-section', 'wiki', 'extension', 'news'];
        } elseif (in_array($type, ['wiki', 'extension', 'news'], true)) {
            $types = [$type];
        } elseif ($type === 'api') {
            $types = ['api-type',/* 'api-primitive',*/];
        } elseif ($type === 'guide') {
            $types = ['guide-section']; // TODO add possibility to search for guide subsections
        } else {
            throw new Exception('Unkown search type given!');
        }

        // TODO detect API names in search string and find that API item

        // add synonyms
        $synonyms = [
            'javascript' => 'Client Script',
            'js' => 'javascript',
            'ar' => 'Active Record',
        ];
        $words = preg_split('~\s+~', ucwords($queryString));
        foreach($words as $word) {
            if (isset($synonyms[$l = mb_strtolower($word)])) {
                $queryString .= ' ' . $synonyms[$l];
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
                ],
                'minimum_should_match' => 1,
//                'boost' => 4,
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
//                    'boost' => 4,
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
                        ['filter' => ['term' => ['version' => '2.0']], "weight" => 4],
                        ['filter' => ['term' => ['version' => '1.1']], "weight" => 2],
                    ],
                ]
            ];
        }
        $query->query($q);
        $query->highlight([
            'fields' => [
                'name' => ["fragment_size" => 5000, "number_of_fragments" => 1],
                'title' => ["fragment_size" => 5000, "number_of_fragments" => 1],
                'title.stemmed' => ["fragment_size" => 5000, "number_of_fragments" => 1],
                'content' => ["fragment_size" => 100, "number_of_fragments" => 5],
                'content.stemmed' => ["fragment_size" => 100, "number_of_fragments" => 5],
            ],
        ]);
        return $query;
    }

    public static function searchAsYouType($queryString, $version = null, $language = null)
    {
        $query = static::find();

        $indexes = [static::index() . '-en'];
        $analyzer = 'english';
        if ($language === null) {
            $indexes = array_map(function($i) { return static::index() . "-$i"; }, array_keys(static::$languages));
            $query->indicesBoost = [
                static::index() . '-en' => 10,
            ];
        } elseif (isset(static::$languages[$language])) {
            $indexes = [static::index() . "-$language"];
            $analyzer = static::$languages[$language];
        }

        // TODO find also dataprovider when searching for "data provider":
        // https://www.elastic.co/guide/en/elasticsearch/guide/current/ngrams-compound-words.html

        $query->from($indexes, [/*'api-type', 'api-primitive',*/ 'guide-section']);
        $query->query([
            'bool' => [
                'must' => [
                    'bool' => [
                        'should' => [
                            // TODO this can be optimized
                            // https://www.elastic.co/guide/en/elasticsearch/guide/current/_index_time_search_as_you_type.html
                            ['match_phrase_prefix' => [
                                'title.stemmed' => [
                                    'query' => $queryString,
                                    'slop' => 10,
                                    'max_expansions' => 15,
                                    'analyzer' => $analyzer,
                                ],
                            ]],
                            ['match_phrase_prefix' => [
                                'title' => [
                                    'query' => $queryString,
                                    'slop' => 10,
                                    'max_expansions' => 50,
                                    'analyzer' => 'standard',
                                ],
                            ]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
                'should' => [
                    // boost english docs
                    ['term' => [
                        'language' => [
                            'value' => 'en',
                            'boost' => 2,
                        ],
                    ]],
                    ['term' => [
                        'version' => [
                            'value' => '2.0',
                            'boost' => 2,
                        ],
                    ]],
                ]
            ]
        ]);
        if ($version !== null) {
            $query->andWhere(['version' => $version]);
        }
        return $query;
    }

    public static function instantiate($row)
    {
        switch($row['_type'])
        {
            case 'api-type': return new SearchApiType();
            case 'api-primitive': return new SearchApiPrimitive();
            case 'guide-section': return new SearchGuideSection();
            case 'extension': return new SearchExtension();
            case 'news': return new SearchNews();
            case 'wiki': return new SearchWiki();
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
}