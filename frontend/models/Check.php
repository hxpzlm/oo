<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%check}}".
 *
 * @property integer $check_id
 * @property string $check_no
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $status
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $create_time
 * @property integer $confirm_user_id
 * @property string $confirm_user_name
 * @property integer $confirm_time
 */
class Check extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%check}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'add_user_id', 'create_time', 'confirm_user_id', 'confirm_time'], 'integer'],
            [['warehouse_id'], 'required', 'message' => '请选择仓库'],
            [['check_no'], 'string', 'max' => 32],
            [['warehouse_name'], 'string', 'max' => 50],
            [['add_user_name', 'confirm_user_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'check_id' => 'Check ID',
            'check_no' => 'Check No',
            'warehouse_id' => 'Warehouse ID',
            'warehouse_name' => 'Warehouse Name',
            'status' => 'Status',
            'add_user_id' => 'Add User ID',
            'add_user_name' => 'Add User Name',
            'create_time' => 'Create Time',
            'confirm_user_id' => 'Confirm User ID',
            'confirm_user_name' => 'Confirm User Name',
            'confirm_time' => 'Confirm Time',
        ];
    }
}
