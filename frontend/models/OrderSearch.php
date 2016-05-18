<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Order;

/**
 * OrderSearch represents the model behind the search form about `frontend\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'store_id', 'warehouse_id', 'shop_id', 'currency_id', 'delivery_id', 'delivery_status', 'address_id', 'confirm_time', 'create_time', 'add_user_id', 'customer_id', 'sale_time', 'confirm_user_id', 'send_time'], 'integer'],
            [['order_no', 'store_name', 'warehouse_name', 'shop_name', 'delivery_name', 'delivey_code', 'remark', 'add_user_name', 'customer_name', 'confirm_user_name'], 'safe'],
            [['real_pay', 'discount'], 'number'],
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
        $query = Order::find();

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
            'order_id' => $this->order_id,
            'store_id' => $this->store_id,
            'warehouse_id' => $this->warehouse_id,
            'shop_id' => $this->shop_id,
            'currency_id' => $this->currency_id,
            'delivery_id' => $this->delivery_id,
            'delivery_status' => $this->delivery_status,
            'real_pay' => $this->real_pay,
            'discount' => $this->discount,
            'address_id' => $this->address_id,
            'confirm_time' => $this->confirm_time,
            'create_time' => $this->create_time,
            'add_user_id' => $this->add_user_id,
            'customer_id' => $this->customer_id,
            'sale_time' => $this->sale_time,
            'confirm_user_id' => $this->confirm_user_id,
            'send_time' => $this->send_time,
        ]);

        $query->andFilterWhere(['like', 'order_no', $this->order_no])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'warehouse_name', $this->warehouse_name])
            ->andFilterWhere(['like', 'shop_name', $this->shop_name])
            ->andFilterWhere(['like', 'delivery_name', $this->delivery_name])
            ->andFilterWhere(['like', 'delivey_code', $this->delivey_code])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name])
            ->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'confirm_user_name', $this->confirm_user_name]);

        return $dataProvider;
    }
}
