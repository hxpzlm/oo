<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "{{%refuse_order}}".
 *
 * @property integer $refuse_id
 * @property string $order_no
 * @property integer $order_id
 * @property integer $store_id
 * @property string $store_name
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $shop_id
 * @property string $shop_name
 * @property integer $sale_time
 * @property string $refuse_amount
 * @property string $reason
 * @property integer $confirm_time
 * @property integer $create_time
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $customer_id
 * @property string $customer_name
 * @property integer $confirm_user_id
 * @property string $confirm_user_name
 * @property string $remark
 * @property integer $refuse_time
 * @property integer $status
 * @property string $refuse_real_pay
 */
class RefuseModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%refuse_order}}';
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
        $query = RefuseModel::find();

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

    public static function getRefuseGoods($refuse_id){
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $db=new\yii\db\Query;
        return $db->select('*')->from("{$tablePrefix}refuse_order_goods")->where(['refuse_id'=>$refuse_id])->all();
    }

}
