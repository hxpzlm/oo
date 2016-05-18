<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%purchase}}".
 *
 * @property integer $purchase_id
 * @property integer $store_id
 * @property string $store_name
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $create_time
 * @property integer $buy_time
 * @property integer $confirm_time
 * @property integer $confirm_user_id
 * @property string $confirm_user_name
 * @property integer $purchases_time
 * @property integer $purchases_status
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $principal_id
 * @property string $principal_name
 * @property string $invoice_and_pay_sate
 * @property string $remark
 * @property string $batch_num
 * @property integer $invalid_time
 * @property string $totle_price
 */
class Purchase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchase}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'warehouse_id', 'create_time', 'add_user_id'], 'integer'],
            [['warehouse_id'], 'required','message'=>'请选择入库仓库'],
            [['totle_price'], 'required','message'=>'总价不能为空'],
            [['invalid_time'], 'required','message'=>'请选择失效日期'],
            [['batch_num'], 'required','message'=>'批号不能为空'],
            [['buy_time'], 'required','message'=>'请选择采购日期'],
            [['principal_id'], 'required','message'=>'请选择负责人'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'purchase_id' => '采购ID',
            'store_id' => '入驻商家ID',
            'store_name' => '入驻商家名称',
            'warehouse_id' => '仓库ID',
            'warehouse_name' => '仓库名称',
            'create_time' => '创建时间 ',
            'buy_time' => '采购日期',
            'confirm_time' => '审核时间',
            'confirm_user_id' => '审核人ID',
            'confirm_user_name' => '审核人姓名',
            'purchases_time' => '入库时间',
            'purchases_status' => '入库状态 0 未入库 1入库',
            'add_user_id' => '创建人ID',
            'add_user_name' => '创建人名称',
            'principal_id' => '负责人',
            'principal_name' => '负责人名称',
            'invoice_and_pay_sate' => '发票和付款情况',
            'remark' => '备注',
            'batch_num' => '批次号',
            'invalid_time' => '失效期',
            'totle_price' => '总价',
        ];
    }

    public static function getGoodsCat($goods_id){
         return Goods::findOne($goods_id);
    }
}
