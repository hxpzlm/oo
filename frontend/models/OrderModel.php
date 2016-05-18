<?php
/**
 * Created by xiegao 销售订单模型.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 14:19
 */
namespace frontend\models;
use Yii;

use yii\data\ActiveDataProvider;
class OrderModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }


    public static function getOrderGoods($order_id){
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = new \yii\db\Query();
       return  $query->select('*')->from($tablePrefix.'order_goods')->where(['order_id'=>$order_id])->all();
    }
    /**
     * @inheritdoc
     */
}