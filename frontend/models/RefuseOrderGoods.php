<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%refuse_order_goods}}".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $barode_code
 * @property string $spec
 * @property integer $unit_id
 * @property string $unit_name
 * @property string $sell_price
 * @property string $batch_num
 * @property integer $number
 * @property integer $stocks_id
 */
class RefuseOrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%refuse_order_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'goods_id', 'brand_id', 'unit_id', 'number', 'stocks_id'], 'integer','message'=>'必须为数字'],
            [['sell_price'], 'number'],
            [['brand_name', 'barode_code', 'spec', 'unit_name', 'batch_num'], 'string', 'max' => 32],
            [['goods_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refuse_id' => 'Refuse ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'barode_code' => 'Barode Code',
            'spec' => 'Spec',
            'unit_id' => 'Unit ID',
            'unit_name' => 'Unit Name',
            'sell_price' => 'Sell Price',
            'batch_num' => 'Batch Num',
            'number' => 'Number',
            'stocks_id' => 'Stocks ID',
        ];
    }
}
