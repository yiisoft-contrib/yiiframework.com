<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 10.04.15
 * Time: 00:24
 */

namespace app\models;


use yii\elasticsearch\ActiveRecord;

abstract class SearchActiveRecord extends ActiveRecord
{
    public static function index()
    {
        return YII_ENV_DEV ? 'yiiframework-dev' : 'yiiframework';
    }

    public static function search($queryString, $version = null, $language = null)
    {
        $query = static::find();
        $query->from(static::index(), ['api-type', 'api-primitive', 'guide-section']);
        $query->query([
            'bool' => [
                'should' => [
                    // match title and description for keywords, boost title by 3
                    ['multi_match' => [
                        'query' => $queryString,
                        'fields' => ['title^3', 'name^3', 'shortDescription^2', 'description', 'body'],
                    ]],
                    // check for comments that match keywords
                    ['has_child' => [
                        'type' => 'api-primitive',
                        'query' => [
                            'match' => ['description' => $queryString],
                        ]
                    ]],
                ],
                'minimum_should_match' => 1
            ],
        ]);
        if ($language !== null) {
            $query->andWhere(['language' => $language]);
        }
        if ($version !== null) {
            $query->andWhere(['version' => $version]);
        }
        $query->highlight([
            'fields' => [
                'shortDescription' => ["fragment_size" => 5000, "number_of_fragments" => 1],
                'description' => ["fragment_size" => 100, "number_of_fragments" => 5],
                'body' => ["fragment_size" => 100, "number_of_fragments" => 5],
            ],
        ]);
        return $query;
    }

    public static function instantiate($row)
    {
        switch($row['_type'])
        {
            case 'api-type': return new SearchApiType();
            case 'api-primitive': return new SearchApiPrimitive();
            case 'guide-section': return new SearchGuideSection();
        }
        return new static;
    }

    public static function deleteAllForVersion($version)
    {
        static::deleteAll(['version' => $version]);
        static::getDb()->createCommand()->flushIndex(static::index());
    }
}