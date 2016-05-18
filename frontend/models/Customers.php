<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%customers}}".
 *
 * @property integer $customers_id
 * @property string $username
 * @property string $real_name
 * @property integer $store_id
 * @property string $store_name
 * @property integer $shop_id
 * @property string $shop_name
 * @property integer $sex
 * @property integer $type
 * @property string $customer_source
 * @property string $mobile
 * @property string $other
 * @property string $address
 * @property integer $sort
 * @property string $remark
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $create_time
 */
class Customers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'shop_id', 'sex', 'type', 'sort', 'add_user_id', 'create_time'], 'integer','message'=>'必须是数字'],
            [['remark'], 'string'],
            [['username', 'mobile', 'add_user_name'], 'string', 'max' => 20],
            [['real_name'], 'string', 'max' => 50],
            [['store_name', 'shop_name'], 'string', 'max' => 32],
            [['customer_source'], 'string', 'max' => 100],
            [['other'], 'string', 'max' => 40],
            [['address'], 'string', 'max' => 80],
            [['shop_id'], 'required','message'=>'请选择客户来源'],
            [['username'], 'required','message'=>'客户帐号不能为空'],
            //[['username'], 'unique', 'targetClass' => '\frontend\models\customers', 'targetAttribute'=>['username'=>'ccc'], 'message' => '客户帐号已存在'],
            [['real_name'], 'required','message'=>'姓名不能为空'],
            [['mobile'], 'required','message'=>'联系电话不能为空'],
            [['sort'], 'required','message'=>'顺序不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customers_id' => '客户ID',
            'username' => '客户账号',
            'real_name' => '客户姓名',
            'store_id' => '入驻商家ID',
            'store_name' => '入驻商家名称',
            'shop_id' => '销售平台ID',
            'shop_name' => '销售平台名称',
            'sex' => '性别 0 女 1 男',
            'type' => '客户类型 0 个人 1企业',
            'customer_source' => '客户来源',
            'mobile' => '联系方式',
            'other' => '客户E-Mail/QQ',
            'address' => '客户地址',
            'sort' => '客户顺序',
            'remark' => '备注',
            'add_user_id' => '创建人ID',
            'add_user_name' => '创建人名称',
            'create_time' => '创建时间',
        ];
    }
}