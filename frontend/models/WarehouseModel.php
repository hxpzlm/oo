<?php
/**
 * Created by xiegao. 仓库模型
 * User: Administrator
 * Date: 2016/4/22
 * Time: 11:06
 */
namespace frontend\models;
use Yii;
class WarehouseModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%warehouse}}';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['store_id', 'sort','principal_id','create_time','add_user_id','status'], 'integer','message'=>'必须为数字'],
            [['remark'], 'string'],
            [['name'], 'string', 'max' => 40],
            [['store_name','principal_name'], 'string', 'max' => 32],
            [['add_user_name'], 'string', 'max' => 20],
            [['name'], 'required', 'message'=>'名称不能为空'],
            [['sort'],'required','message'=>'排序不能为空'],
            [['status'],'required','message'=>'请选择状态'],
            [['principal_id'], 'required','message'=>'负责人不能为空'],
            [['name'], 'unique', 'message' => '名称已存在.'],
        ];
    }

}
