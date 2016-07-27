<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "s2_expressway".
 *
 * @property integer $delivery_id
 * @property string $name
 * @property integer $sort
 * @property integer $status
 * @property integer $store_id
 * @property string $remark
 */
class Expressway extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%expressway}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required','message'=>'名称不能为空'],
            [['sort', 'status', 'store_id','add_user_id', 'create_time'], 'integer','message'=>'必须是数字'],
            [['remark'], 'string'],
            [['add_user_name'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique', 'filter'=>['store_id'=>Yii::$app->user->identity->store_id], 'message' => '名称已存在.'],
            [['sort'], 'required','message'=>'顺序不能为空'],
            [['status'], 'required','message'=>'顺序不能为空'],
        ];
    }

}
