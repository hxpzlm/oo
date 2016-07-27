<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%shop}}".
 *
 * @property integer $shop_id
 * @property string $name
 * @property integer $store_id
 * @property string $store_name
 * @property string $url
 * @property integer $sort
 * @property integer $status
 * @property string $remark
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $create_time
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'sort', 'status', 'add_user_id', 'create_time'], 'integer'],
            [['remark'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['store_name'], 'string', 'max' => 32],
            [['url'], 'string', 'max' => 100],
            [['add_user_name'], 'string', 'max' => 20],
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required','message'=>'平台名称不能为空'],
            [['name'], 'unique', 'filter'=>['store_id'=>Yii::$app->user->identity->store_id], 'message'=>'平台名称已存在'],
            ['sort', 'required','message'=>'顺序不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shop_id' => '平台ID',
            'name' => '平台名称',
            'store_id' => '入驻商家ID',
            'store_name' => '入驻商家名称',
            'url' => '平台网址',
            'sort' => '排序',
            'status' => '状态 0 停用 1启用',
            'remark' => '备注说明',
            'add_user_id' => '创建人ID',
            'add_user_name' => '创建人名称',
            'create_time' => '创建时间',
        ];
    }
}
