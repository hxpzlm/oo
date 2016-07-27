<?php

namespace frontend\models;

use Yii;

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
class RefuseOrder extends \yii\db\ActiveRecord
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
    public function rules()
    {
        return [
            [['refuse_id', 'order_id', 'warehouse_id', 'shop_id', 'sale_time', 'create_time', 'customer_id','order_no'], 'integer','message'=>'必须为数字'],
            [['reason', 'remark'], 'string'],
            [['order_no', 'store_name', 'warehouse_name', 'shop_name', 'add_user_name', 'confirm_user_name'], 'string', 'max' => 32],
            ['shop_id', 'required','message'=>'请选择销售平台'],
            ['order_no', 'required','message'=>'请选择订单编号'],
            ['customer_name', 'string','max' => 100],
            ['refuse_amount', 'required','message'=>'退款金额不能为空'],
            ['refuse_amount', 'number','message'=>'退款金额格式不正确'],
            ['refuse_time', 'required','message'=>'请选择退款日期'],
            ['warehouse_id', 'required','message'=>'请选择仓库'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'refuse_id' => 'Refuse ID',
            'order_no' => 'Order No',
            'order_id' => 'Order ID',
            'store_id' => 'Store ID',
            'store_name' => 'Store Name',
            'warehouse_id' => 'Warehouse ID',
            'warehouse_name' => 'Warehouse Name',
            'shop_id' => 'Shop ID',
            'shop_name' => 'Shop Name',
            'sale_time' => 'Sale Time',
            'refuse_amount' => 'Refuse Amount',
            'reason' => 'Reason',
            'confirm_time' => 'Confirm Time',
            'create_time' => 'Create Time',
            'add_user_id' => 'Add User ID',
            'add_user_name' => 'Add User Name',
            'customer_id' => 'Customer ID',
            'customer_name' => 'Customer Name',
            'confirm_user_id' => 'Confirm User ID',
            'confirm_user_name' => 'Confirm User Name',
            'remark' => 'Remark',
            'refuse_time' => 'Refuse Time',
            'status' => 'Status',
            'refuse_real_pay' => 'Refuse Real Pay',
        ];
    }
}
