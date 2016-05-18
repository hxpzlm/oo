<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Purchase;

/**
 * PurchaseSearch represents the model behind the search form about `frontend\models\Purchase`.
 */
class PurchaseSearch extends Purchase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchase_id', 'store_id', 'warehouse_id', 'create_time', 'buy_time', 'confirm_time', 'confirm_user_id', 'purchases_time', 'purchases_status', 'add_user_id', 'principal_id', 'invalid_time'], 'integer'],
            [['store_name', 'warehouse_name', 'confirm_user_name', 'add_user_name', 'principal_name', 'invoice_and_pay_sate', 'remark', 'batch_num'], 'safe'],
            [['totle_price'], 'number'],
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
        $query = Purchase::find();

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
            'purchase_id' => $this->purchase_id,
            'store_id' => $this->store_id,
            'warehouse_id' => $this->warehouse_id,
            'create_time' => $this->create_time,
            'buy_time' => $this->buy_time,
            'confirm_time' => $this->confirm_time,
            'confirm_user_id' => $this->confirm_user_id,
            'purchases_time' => $this->purchases_time,
            'purchases_status' => $this->purchases_status,
            'add_user_id' => $this->add_user_id,
            'principal_id' => $this->principal_id,
            'invalid_time' => $this->invalid_time,
            'totle_price' => $this->totle_price,
        ]);

        $query->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'warehouse_name', $this->warehouse_name])
            ->andFilterWhere(['like', 'confirm_user_name', $this->confirm_user_name])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name])
            ->andFilterWhere(['like', 'principal_name', $this->principal_name])
            ->andFilterWhere(['like', 'invoice_and_pay_sate', $this->invoice_and_pay_sate])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'batch_num', $this->batch_num]);

        return $dataProvider;
    }
}
