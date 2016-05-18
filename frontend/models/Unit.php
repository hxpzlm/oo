<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "s2_unit".
 *
 * @property integer $unit_id
 * @property string $unit
 * @property string $remark
 * @property integer $sort
 */
class Unit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%unit}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id'], 'required','message'=>'计量单位ID不能为空'],
            [['unit_id', 'sort','add_user_id', 'create_time'], 'integer','message'=>'必须为数字'],
            [['add_user_name'], 'string', 'max' => 20],
            [['remark'], 'string','max' => 256,'message'=>'不能超过256个字符'],
            [['unit'], 'string', 'max' => 100,'message'=>'不能超过100个字符'],
            [['unit'], 'required', 'message'=>'计量单位名称不能为空'],
            [['sort'],'required','message'=>'排序不能为空'],
            [['unit_id','unit'], 'unique', 'message' => '已存在.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'unit_id' => Yii::t('app', ''),//计量单位id
            'unit' => Yii::t('app', ''),//计量单位名称
            'remark' => Yii::t('app', ''),//备注说明
            'sort' => Yii::t('app', ''),//排序
        ];
    }

}
