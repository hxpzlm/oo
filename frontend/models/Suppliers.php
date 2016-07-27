<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%suppliers}}".
 *
 * @property integer $suppliers_id
 * @property string $name
 * @property integer $store_id
 * @property string $store_name
 * @property string $country
 * @property string $city
 * @property string $contact_man
 * @property string $mobile
 * @property string $tel
 * @property string $email
 * @property string $fax
 * @property string $address
 * @property string $shop_manage_principal
 * @property string $remark
 * @property integer $create_time
 * @property integer $add_user_id
 * @property integer $sort
 * @property string $add_user_name
 */
class Suppliers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%suppliers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'create_time', 'add_user_id', 'sort','status'], 'integer'],
            [['remark'], 'string'],
            [['name', 'email'], 'string', 'max' => 40],
            [['store_name', 'country', 'city', 'shop_manage_principal'], 'string', 'max' => 32],
            [['contact_man', 'mobile', 'fax', 'add_user_name'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 100],
            ['name', 'required','message'=>'供应商名称不能为空'],
            [['name'], 'unique', 'filter'=>['store_id'=>Yii::$app->user->identity->store_id], 'message' => '供应商名称已存在.'],
            ['country', 'required','message'=>'请选择国别'],
            ['contact_man', 'required','message'=>'联系人不能为空'],
            ['mobile', 'required','message'=>'电话不能为空'],
            ['email', 'required','message'=>'邮箱不能为空'],
            ['shop_manage_principal', 'required','message'=>'请选择负责人'],
            ['sort', 'required','message'=>'顺序不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            //'suppliers_id' => 'Suppliers ID',
            'name' => '供应商名称',
            //'store_id' => 'Store ID',
            //'store_name' => 'Store Name',
            'country' => '国别',
            'city' => '城市名',
            'contact_man' => '联系人',
            'mobile' => '电话',
            //'tel' => 'Tel',
            'email' => '邮箱',
            'fax' => '传真',
            'address' => '地址',
            'shop_manage_principal' => '负责人',
            'remark' => '备注说明',
            //'create_time' => 'Create Time',
            //'add_user_id' => 'Add User ID',
            'sort' => '顺序',
            //'add_user_name' => 'Add User Name',
        ];
    }
}
