<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Shop;

/**
 * ShopSearch represents the model behind the search form about `frontend\models\Shop`.
 */
class ShopSearch extends Shop
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'store_id', 'sort', 'status', 'add_user_id', 'create_time'], 'integer'],
            [['name', 'store_name', 'url', 'remark', 'add_user_name'], 'safe'],
        ];
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
        $query = Shop::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'shop_id' => $this->shop_id,
            'store_id' => $this->store_id,
            'sort' => $this->sort,
            'status' => $this->status,
            'add_user_id' => $this->add_user_id,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name]);

        return $dataProvider;
    }
}
