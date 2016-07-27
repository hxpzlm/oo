<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "s2_brand".
 *
 * @property integer $brand_id
 * @property string $name
 * @property integer $store_id
 * @property string $store_name
 * @property integer $sort
 * @property integer $status
 * @property string $remark
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $create_time
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'sort', 'status', 'add_user_id', 'create_time'], 'integer','message'=>'必须是数字'],
            [['remark'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['store_name'], 'string', 'max' => 32],
            [['add_user_name'], 'string', 'max' => 20],
            [['name'], 'required','message'=>'品牌名称不能为空'],
            [['name'], 'unique', 'filter'=>['store_id'=>Yii::$app->user->identity->store_id], 'message' => '品牌名称已存在.'],
            [['name'], 'string', 'min' => 2, 'max' => 255],
            [['sort'], 'required','message'=>'顺序不能为空'],
            [['status'], 'required','message'=>'状态不能为空'],
        ];
    }
    public function attributeLabels()

    {
        return [
            'store_id' => Yii::t('app', ''),
            'name' => Yii::t('app', ''),
            'status' => Yii::t('app', ''),
            'remark' => Yii::t('app', ''),
            'sort' => Yii::t('app', ''),
        ];
    }

}
