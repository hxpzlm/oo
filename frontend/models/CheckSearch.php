<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Check;

/**
 * CheckSearch represents the model behind the search form about `frontend\models\Check`.
 */
class CheckSearch extends Check
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_id', 'warehouse_id', 'store_id', 'status', 'add_user_id', 'create_time', 'confirm_user_id', 'confirm_time'], 'integer'],
            [['check_no', 'warehouse_name', 'store_name', 'add_user_name', 'confirm_user_name'], 'safe'],
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
        $query = Check::find();

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
            'check_id' => $this->check_id,
            'warehouse_id' => $this->warehouse_id,
            'store_id' => $this->store_id,
            'status' => $this->status,
            'add_user_id' => $this->add_user_id,
            'create_time' => $this->create_time,
            'confirm_user_id' => $this->confirm_user_id,
            'confirm_time' => $this->confirm_time,
        ]);

        $query->andFilterWhere(['like', 'check_no', $this->check_no])
            ->andFilterWhere(['like', 'warehouse_name', $this->warehouse_name])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name])
            ->andFilterWhere(['like', 'confirm_user_name', $this->confirm_user_name]);

        return $dataProvider;
    }
}
