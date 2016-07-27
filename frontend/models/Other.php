<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%other}}".
 *
 * @property integer $other_id
 * @property integer $store_id
 * @property string $store_name
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $status
 * @property string $remark
 * @property integer $confirm_time
 * @property integer $create_time
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $check_time
 * @property integer $confirm_user_id
 * @property string $confirm_user_name
 * @property integer $check_status
 * @property integer $check_user_id
 * @property string $chekc_user_name
 */
class Other extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%other}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'warehouse_id', 'status', 'confirm_time', 'create_time', 'add_user_id', 'check_time', 'confirm_user_id', 'check_status', 'check_user_id'], 'integer'],
            [['remark'], 'string'],
            [['store_name', 'warehouse_name', 'add_user_name', 'confirm_user_name', 'chekc_user_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'other_id' => 'Other ID',
            'store_id' => 'Store ID',
            'store_name' => 'Store Name',
            'warehouse_id' => 'Warehouse ID',
            'warehouse_name' => 'Warehouse Name',
            'status' => 'Status',
            'remark' => 'Remark',
            'confirm_time' => 'Confirm Time',
            'create_time' => 'Create Time',
            'add_user_id' => 'Add User ID',
            'add_user_name' => 'Add User Name',
            'check_time' => 'Check Time',
            'confirm_user_id' => 'Confirm User ID',
            'confirm_user_name' => 'Confirm User Name',
            'check_status' => 'Check Status',
            'check_user_id' => 'Check User ID',
            'chekc_user_name' => 'Chekc User Name',
        ];
    }
}
