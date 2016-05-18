<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $order_id
 * @property string $order_no
 * @property integer $store_id
 * @property string $store_name
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $shop_id
 * @property string $shop_name
 * @property integer $currency_id
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivey_code
 * @property integer $delivery_status
 * @property double $real_pay
 * @property string $discount
 * @property integer $address_id
 * @property string $remark
 * @property integer $confirm_time
 * @property integer $create_time
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $customer_id
 * @property string $customer_name
 * @property integer $sale_time
 * @property integer $confirm_user_id
 * @property string $confirm_user_name
 * @property integer $send_time
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @address_id
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'warehouse_id', 'shop_id', 'address_id', 'create_time', 'customer_id'], 'integer'],
            [['real_pay', 'discount'], 'number'],
            [['remark'], 'string'],
            [['order_no', 'store_name', 'warehouse_name', 'shop_name', 'add_user_name', 'customer_name', 'confirm_user_name'], 'string', 'max' => 32],
            [['delivery_name'], 'string', 'max' => 60],
            [['warehouse_id'], 'required', 'message' => '请选择销售订单出货仓库'],
            [['shop_id'], 'required', 'message' => '请选择销售平台'],
            [['order_no'], 'required', 'message' => '订单编号不能为空'],
           // [['order_no'], 'unique', 'targetClass' => '\frontend\models\order', 'message' => '订单编号已存在'],
            [['real_pay'], 'required', 'message' => '实收款不能为空'],
            [['customer_name'], 'required', 'message' => '请选择客户帐号'],
            //[['address_id'], 'required', 'message' => '请选择收货人姓名'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'order_no' => '订单编号',
            'store_id' => '入驻商家ID',
            'store_name' => '入驻商家名称',
            'warehouse_id' => '仓库ID',
            'warehouse_name' => '仓库名称',
            'shop_id' => '销售平台ID',
            'shop_name' => '销售平台名称',
            'currency_id' => '币种ID',
            'delivery_id' => '物流公司',
            'delivery_name' => '物流企业名称',
            'delivey_code' => '物流单号',
            'delivery_status' => '是否出库 0未出库 1出库',
            'real_pay' => '实收款',
            'discount' => '优惠',
            'address_id' => '收货人地址ID',
            'remark' => '备注',
            'confirm_time' => '审核时间',
            'create_time' => '创建时间',
            'add_user_id' => '创建人ID',
            'add_user_name' => '创建人名称',
            'customer_id' => '客户ID',
            'customer_name' => '客户名称',
            'sale_time' => '销售时间',
            'confirm_user_id' => '审核人ID',
            'confirm_user_name' => '审核人名称',
            'send_time' => '出库时间',
        ];
    }
}
