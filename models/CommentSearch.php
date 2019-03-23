<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CommentSearch represents the model behind the search form of `app\models\Comment`.
 */
class CommentSearch extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'total_votes', 'up_votes'], 'integer'],
            [['object_type', 'object_id', 'text', 'created_at', 'updated_at', 'user.username'], 'string'],
            [['rating'], 'number'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['user.username']);
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
        $query = Comment::find()->joinWith('user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'user.username',
                    'object_type',
                    'object_id',
                    'status',
                    'created_at',
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
            'comment.id' => $this->id,
            'comment.user_id' => $this->user_id,
            'comment.status' => $this->status,
            'comment.created_at' => $this->created_at,
            'comment.updated_at' => $this->updated_at,
            'comment.total_votes' => $this->total_votes,
            'comment.up_votes' => $this->up_votes,
            'comment.rating' => $this->rating,
            'comment.object_type' => $this->object_type,
            'comment.object_id' => $this->object_id,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);
        $query->andFilterWhere(['like', 'user.username', $this->getAttribute('user.username'),
        ]);

        return $dataProvider;
    }
}
