<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\RefuseOrder;

/**
 * RefuseOrderSearch represents the model behind the search form about `frontend\models\RefuseOrder`.
 */
class RefuseOrderSearch extends RefuseOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['refuse_id', 'order_id', 'store_id', 'warehouse_id', 'shop_id', 'sale_time', 'confirm_time', 'create_time', 'add_user_id', 'customer_id', 'confirm_user_id', 'refuse_time', 'status'], 'integer'],
            [['order_no', 'store_name', 'warehouse_name', 'shop_name', 'reason', 'add_user_name', 'customer_name', 'confirm_user_name', 'remark'], 'safe'],
            [['refuse_amount', 'refuse_real_pay'], 'number'],
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
        $query = RefuseOrder::find();

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
            'refuse_id' => $this->refuse_id,
            'order_id' => $this->order_id,
            'store_id' => $this->store_id,
            'warehouse_id' => $this->warehouse_id,
            'shop_id' => $this->shop_id,
            'sale_time' => $this->sale_time,
            'refuse_amount' => $this->refuse_amount,
            'confirm_time' => $this->confirm_time,
            'create_time' => $this->create_time,
            'add_user_id' => $this->add_user_id,
            'customer_id' => $this->customer_id,
            'confirm_user_id' => $this->confirm_user_id,
            'refuse_time' => $this->refuse_time,
            'status' => $this->status,
            'refuse_real_pay' => $this->refuse_real_pay,
        ]);

        $query->andFilterWhere(['like', 'order_no', $this->order_no])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'warehouse_name', $this->warehouse_name])
            ->andFilterWhere(['like', 'shop_name', $this->shop_name])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name])
            ->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'confirm_user_name', $this->confirm_user_name])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
