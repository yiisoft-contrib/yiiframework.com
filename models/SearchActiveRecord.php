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

    public static function search($queryString)
    {
        $query = static::find();
        $query->from(static::index(), ['api-type', 'api-primitive']);
        $query->query([
            'bool' => [
                'should' => [
                    // match title and description for keywords, boost title by 3
                    ['multi_match' => [
                        'query' => $queryString,
                        'fields' => ['name^3', 'shortDescription^2', 'description'],
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
        $query->highlight([
            'fields' => [
                'shortDescription' => ["fragment_size" => 5000, "number_of_fragments" => 1],
                'description' => ["fragment_size" => 100, "number_of_fragments" => 5],
            ],
        ]);
        return $query;
    }

    public static function instantiate($row)
    {
        switch($row['_type'])
        {
            case 'api-type': return new ApiType();
            case 'api-primitive': return new ApiPrimitive();
        }
        return new static;
    }

}