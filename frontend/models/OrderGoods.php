<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%order_goods}}".
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
 * @property integer $refuse_id
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id'], 'required'],
            [['id', 'goods_id', 'brand_id', 'unit_id', 'number', 'stocks_id', 'refuse_id'], 'integer'],
            [['sell_price'], 'number'],
            [['order_id', 'brand_name', 'barode_code', 'spec', 'unit_name', 'batch_num'], 'string', 'max' => 32],
            [['goods_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单ID',
            'order_id' => '订单ID ',
            'goods_id' => '商品ID',
            'goods_name' => '商品名称',
            'brand_id' => '品牌ID',
            'brand_name' => '品牌名称',
            'barode_code' => '商品条形码',
            'spec' => '规格',
            'unit_id' => '计量单位ID',
            'unit_name' => '计量单位名称  ',
            'sell_price' => '销售单价',
            'batch_num' => '批次号',
            'number' => '销售数量',
            'stocks_id' => '库存ID',
            'refuse_id' => '退货订单ID',
        ];
    }
}
