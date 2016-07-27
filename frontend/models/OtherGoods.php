<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%other_goods}}".
 *
 * @property integer $id
 * @property string $other_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $spec
 * @property integer $unit_id
 * @property string $unit_name
 * @property string $batch_num
 * @property integer $number
 * @property integer $stocks_id
 */
class OtherGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%other_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['other_id'], 'required'],
            [['goods_id', 'brand_id', 'unit_id', 'number', 'stocks_id'], 'integer'],
            [['other_id', 'brand_name', 'spec', 'unit_name', 'batch_num'], 'string', 'max' => 32],
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
            'other_id' => 'Other ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'spec' => 'Spec',
            'unit_id' => 'Unit ID',
            'unit_name' => 'Unit Name',
            'batch_num' => 'Batch Num',
            'number' => 'Number',
            'stocks_id' => 'Stocks ID',
        ];
    }
}
