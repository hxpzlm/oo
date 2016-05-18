<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Moving;

/**
 * MovingSearch represents the model behind the search form about `frontend\models\Moving`.
 */
class MovingSearch extends Moving
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['moving_id', 'from_warehouse_id', 'to_warehouse_id', 'store_id', 'goods_id', 'brand_id', 'unit_id', 'number', 'update_time', 'confirm_time', 'add_user_id', 'create_time', 'confirm_user_id', 'status'], 'integer'],
            [['from_warehouse_name', 'to_warehouse_name', 'store_name', 'goods_name', 'brand_name', 'barode_code', 'spec', 'remark', 'batch_num', 'add_user_name', 'confirm_user_name', 'unit_name'], 'safe'],
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
        $query = Moving::find();

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
            'moving_id' => $this->moving_id,
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id' => $this->to_warehouse_id,
            'store_id' => $this->store_id,
            'goods_id' => $this->goods_id,
            'brand_id' => $this->brand_id,
            'unit_id' => $this->unit_id,
            'number' => $this->number,
            'update_time' => $this->update_time,
            'confirm_time' => $this->confirm_time,
            'add_user_id' => $this->add_user_id,
            'create_time' => $this->create_time,
            'confirm_user_id' => $this->confirm_user_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'from_warehouse_name', $this->from_warehouse_name])
            ->andFilterWhere(['like', 'to_warehouse_name', $this->to_warehouse_name])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'goods_name', $this->goods_name])
            ->andFilterWhere(['like', 'brand_name', $this->brand_name])
            ->andFilterWhere(['like', 'barode_code', $this->barode_code])
            ->andFilterWhere(['like', 'spec', $this->spec])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'batch_num', $this->batch_num])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name])
            ->andFilterWhere(['like', 'confirm_user_name', $this->confirm_user_name])
            ->andFilterWhere(['like', 'unit_name', $this->unit_name]);

        return $dataProvider;
    }
}
