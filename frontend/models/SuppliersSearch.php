<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Suppliers;

/**
 * SuppliersSearch represents the model behind the search form about `frontend\models\Suppliers`.
 */
class SuppliersSearch extends Suppliers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['suppliers_id', 'store_id', 'create_time', 'add_user_id', 'sort'], 'integer'],
            [['name', 'store_name', 'country', 'city', 'contact_man', 'mobile', 'tel', 'email', 'fax', 'address', 'shop_manage_principal', 'remark', 'add_user_name'], 'safe'],
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
        $query = Suppliers::find();

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
            'suppliers_id' => $this->suppliers_id,
            'store_id' => $this->store_id,
            'create_time' => $this->create_time,
            'add_user_id' => $this->add_user_id,
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'store_name', $this->store_name])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'contact_man', $this->contact_man])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'shop_manage_principal', $this->shop_manage_principal])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'add_user_name', $this->add_user_name]);

        return $dataProvider;
    }
}
