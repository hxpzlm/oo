<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%warning_info}}".
 *
 * @property integer $id
 * @property string $info
 * @property integer $warning_id
 * @property integer $warning_num
 * @property integer $warning_time
 * @property integer $is_send
 * @property integer $close_type
 * @property integer $close_time
 */
class WarningInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%warning_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info'], 'required'],
            [['info'], 'string'],
            [['warning_num', 'warning_time', 'is_send', 'close_type', 'close_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'info' => 'Info',
            'warning_id' => 'Warning ID',
            'warning_num' => 'Warning Num',
            'warning_time' => 'Warning Time',
            'is_send' => 'Is Send',
            'close_type' => 'Close Type',
            'close_time' => 'Close Time',
        ];
    }
}
