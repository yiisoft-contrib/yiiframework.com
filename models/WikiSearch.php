<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WikiSearch represents the model behind the search form about `app\models\Wiki`.
 */
class WikiSearch extends Wiki
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'yii_version', 'created_at', 'updated_at'], 'string'],
            ['category.name', 'in', 'range' => array_keys(static::getCategoryFilter())],
            ['status', 'in', 'range' => array_keys(static::getStatuses())],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['category.name']);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Wiki::find()->joinWith('category AS category');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'title',
                    'status',
                    'category.name',
                    'yii_version',
                    'created_at',
                    'updated_at',
                ],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'wiki.id' => $this->id,
            'status' => $this->status,
            'category_id' => $this->getAttribute('category.name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'yii_version', $this->yii_version]);

        return $dataProvider;
    }

    public static function getCategoryFilter()
    {
        return WikiCategory::find()->select('name')->indexBy('id')->column();
    }
}
