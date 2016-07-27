<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "s2_category".
 *
 * @property integer $cat_id
 * @property string $name
 * @property integer $store_id
 * @property string $store_name
 * @property integer $parent_id
 * @property integer $sort
 * @property string $remark
 * @property integer $create_time
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $status
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required','message'=>'分类名称不能为空'],
            [['store_id', 'parent_id', 'sort', 'create_time', 'add_user_id', 'status'], 'integer','message'=>'必须为数字'],
            [['remark'], 'string'],
            [['name', 'add_user_name'], 'string', 'max' => 20],
            [['store_name'], 'string', 'max' => 32],
            [['sort'], 'required', 'message'=>'排序不能为空'],
            //[['name'], 'unique', 'filter'=>['store_id'=>Yii::$app->user->identity->store_id], 'message' => '分类名称已存在.'],
        ];
}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => Yii::t('app', ''),//分类ID
            'name' => Yii::t('app', ''),//分类名称
            'store_id' => Yii::t('app', ''),//入驻商家ID
            'store_name' => Yii::t('app', ''),//入住商家名称
            'parent_id' => Yii::t('app', ''),//父分类
            'sort' => Yii::t('app', ''),//分类排序
            'remark' => Yii::t('app', ''),//备注说明
            'create_time' => Yii::t('app', ''),//创建时间
            'add_user_id' => Yii::t('app', ''),//创建人id
            'add_user_name' => Yii::t('app', ''),//创建人姓名
            'status' => Yii::t('app', ''),//状态 1正常 0 停用
        ];
    }
}
