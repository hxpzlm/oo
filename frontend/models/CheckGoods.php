<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%check_goods}}".
 *
 * @property integer $id
 * @property integer $check_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $spec
 * @property string $batch_num
 * @property integer $stocks_num
 * @property integer $check_num
 * @property string $remark
 */
class CheckGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%check_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_id', 'goods_id', 'stocks_num', 'check_num'], 'integer'],
            [['remark'], 'string'],
            [['goods_name'], 'string', 'max' => 255],
            [['spec', 'batch_num'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_id' => 'Check ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'spec' => 'Spec',
            'batch_num' => 'Batch Num',
            'stocks_num' => 'Stocks Num',
            'check_num' => 'Check Num',
            'remark' => 'Remark',
        ];
    }
}
