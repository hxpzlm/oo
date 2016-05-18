<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/22
 * Time: 9:03
 */
/**
 * Created by xiegao 销售订单模型.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 14:19
 */
namespace frontend\models;
use Yii;
use yii\data\ActiveDataProvider;
class MoveModel extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%moving}}';
    }

    /**
     * @inheritdoc
     */
}