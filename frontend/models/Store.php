<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%store}}".
 *
 * @property integer $store_id
 * @property string $name
 * @property integer $sort
 * @property integer $status
 * @property string $remark
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $create_time
 */
class Store extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'status', 'add_user_id', 'create_time'], 'integer','message' => '必须为数字'],
            [['remark'], 'string','max' => 256,],
            [['name'], 'string', 'max' => 50,],
            [['name'], 'unique', 'message' => '商家名称已存在.'],
            [['name'], 'required', 'message' => '入驻商家名称不能为空'],
            [['add_user_name'], 'string', 'max' => 20],
            [['sort'], 'required','message'=>'排序不能为空'],
            [['status'], 'required','message'=>'状态不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'store_id' => Yii::t('app', '入驻商家ID'),
            'name' => Yii::t('app', ''),
            'sort' => Yii::t('app', ''),
            'status' => Yii::t('app', ''),
            'remark' => Yii::t('app', ''),//备注说明
            'add_user_id' => Yii::t('app', '创建人ID'),
            'add_user_name' => Yii::t('app', '创建人名称'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }
}
