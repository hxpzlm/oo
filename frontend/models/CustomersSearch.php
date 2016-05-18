<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Customers;

/**
 * CustomersSearch represents the model behind the search form about `frontend\models\Customers`.
 */
class CustomersSearch extends Customers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customers_id', 'store_id', 'shop_id', 'sex', 'type', 'sort', 'add_user_id', 'create_time'], 'integer'],
            [['username', 'real_name', 'store_name', 'shop_name', 'customer_source', 'mobile', 'other', 'address', 'remark', 'add_user_name'], 'safe'],
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
        $query = Customers::find();

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
            'customers_id' => $this->customers_id,
            'store_id' => $this->store_id,
            'shop_id' => $this->shop_id,
            'sex' => $this->sex,
            'type' => $this->type,
            'sort' => $this->sort,
            'add_user_id' => $this->add_user_id,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'real_name', $this->real_name])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'shop_name', $this->shop_name])
            ->andFilterWhere(['like', 'customer_source', $this->customer_source])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'other', $this->other])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name]);

        return $dataProvider;
    }
}