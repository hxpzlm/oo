<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%purchase_goods}}".
 *
 * @property integer $id
 * @property integer $purchase_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $spec
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $barode_code
 * @property integer $unit_id
 * @property string $unit_name
 * @property string $buy_price
 * @property integer $number
 * @property integer $supplier_id
 * @property string $supplier_name
 */
class PurchaseGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchase_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'brand_id', 'unit_id', 'number', 'supplier_id'], 'integer'],
            [['buy_price'], 'number'],
            [['goods_name'], 'string', 'max' => 100],
            [['spec', 'barode_code'], 'string', 'max' => 20],
            [['brand_name', 'unit_name'], 'string', 'max' => 40],
            [['supplier_name'], 'string', 'max' => 50],
            [['goods_name'], 'required','message'=>'商品中英文名称不能为空'],
            [['buy_price'], 'required','message'=>'采购单价不能为空'],
            [['number'], 'required','message'=>'采购数量不能为空'],
            [['supplier_name'], 'required','message'=>'请选择供应商'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_id' => 'Purchase ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'spec' => 'Spec',
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'barode_code' => 'Barode Code',
            'unit_id' => 'Unit ID',
            'unit_name' => 'Unit Name',
            'buy_price' => 'Buy Price',
            'number' => 'Number',
            'supplier_id' => 'Supplier ID',
            'supplier_name' => 'Supplier Name',
        ];
    }
}
