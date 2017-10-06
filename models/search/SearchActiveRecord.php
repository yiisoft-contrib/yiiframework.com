<?php

namespace app\models\search;


use Yii;
use yii\base\Exception;

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

        $query->from($indexes, $types);
        $q = [
            'bool' => [
                'should' => [
                    [
                        // exact match on name field, which is a keyword and not analyzed
                        'match' => [
                            'name' => $queryString,
                        ],
                    ],
                    [
                        // analyzed match on title and content, boost exact matches over typos
                        'multi_match' => [
                            'query' => $queryString,
                            'analyzer' => $analyzer,
                            'fields' => [
                                'title^3',
                                'content^2',
                            ],
                            'type' => 'most_fields',
                        ],
                    ],
//                    [
//                        // phrase match on title and content, boost exact matches over typos
//                        'multi_match' => [
//                            'query' => $queryString,
//                            'analyzer' => $analyzer,
//                            'fields' => [
//                                'title^2',
//                                'content^2',
//                            ],
//                            'type' => 'phrase',
//                        ],
//                    ],
                    [
                        // analyzed match on content, allow fuzzyness, i.e. typos in the words
                        'multi_match' => [
                            'query' => $queryString,
                            'analyzer' => $analyzer,
                            // https://www.elastic.co/guide/en/elasticsearch/reference/5.6/common-options.html#fuzziness
                            'fuzziness' => 'AUTO',
                            'fields' => [
                                'content',
                            ],
                            'type' => 'most_fields',
                        ],
                    ],
//                    [
//                        // analyzed match on title and content, using phrase query
//                        'match_phrase' => [
//                            'title' => [
//                                'query' => $queryString,
//                                'analyzer' => $analyzer,
//                            ],
//                        ],
//                    ],

                    // TODO match_phrase would be nice maybe

                    // match title and description for keywords, boost title by 3
//                    ['multi_match' => [
//                        'query' => $queryString,
//                        'fields' => [
//                            // match title indexed by analyzer
//                            'title.stemmed^5',
//                            // match name exactly unstemmed (name is from API doc)
//                            'name^5',
////                            'shortDescription^2',
////                            'description',
//                            'content.stemmed^2',
//                        ],
//                        //'operator' => 'and',
//                        //'minimum_should_match' => '75%',
//                        'type' => 'most_fields',
//                        'analyzer' => $analyzer,
//                        'fuzziness' =>  '2',
//                    ]],
//                    ['multi_match' => [
//                        'query' => $queryString,
//                        'fields' => [
//                            // match the unanalyzed title to match exact words
//                            'title.stemmed^3',
//                            'title^3',
//                            // match name exactly unstemmed (name is from API doc)
//                            'name^3',
//                            'shortDescription^2',
//                            'content',
//                            'content.stemmed',
//                        ],
//                        //'operator' => 'and',
//                        'minimum_should_match' => '20%',
//                        'type' => 'most_fields',
//                        'analyzer' => 'standard',
//                        'fuzziness' =>  '2',
//                    ]],
                    // check for comments that match keywords
// TODO
//                    ['has_child' => [
//                        'type' => 'api-primitive',
//                        'query' => [
//                            'match' => ['description' => $queryString],
//                        ]
//                    ]],
                ],
                'minimum_should_match' => 1
            ],
        ];
        if ($version !== null) {
            $q['bool']['filter'] = ['term' => ['version' => $version]];
        }
        $query->query($q);
        $query->highlight([
            'fields' => [
//                'shortDescription' => ["fragment_size" => 5000, "number_of_fragments" => 1],
                'content' => ["fragment_size" => 100, "number_of_fragments" => 5],
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