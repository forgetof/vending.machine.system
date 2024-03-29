<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Box;

/**
 * BoxSearch represents the model behind the search form of `common\models\Box`.
 */
class BoxSearch extends Box
{

    public $name;
    public $price;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'code', 'store_id'], 'integer'],
            [['status'], 'safe'],
            [['status', 'name'], 'trim'],
            [['name'], 'safe'],
            [['price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Box::find()->where(['box.store_id' => $params['id']]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        if ($this->name) {
            $query->joinWith('product');
        }

        $query->andFilterWhere(['like', 'product.name', $this->name])
            ->andFilterWhere(['code' => $this->code,]);

        return $dataProvider;
    }

    public function getEmptyBoxQuantity($store_id)
    {
        $query = Box::find()
            ->where(['store_id' => $store_id])
            ->andWhere(['status' => Box::BOX_STATUS_EMPTY]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);

        return $dataProvider->getTotalCount();
    }
}
