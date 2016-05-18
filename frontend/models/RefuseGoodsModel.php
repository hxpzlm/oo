<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/20
 * Time: 17:20
 */
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RefuseGoodsModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%refuse_order_goods}}';
    }
}