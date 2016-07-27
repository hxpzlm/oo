<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%moving}}".
 *
 * @property integer $moving_id
 * @property integer $from_warehouse_id
 * @property string $from_warehouse_name
 * @property integer $to_warehouse_id
 * @property string $to_warehouse_name
 * @property integer $store_id
 * @property string $store_name
 * @property integer $goods_id
 * @property string $goods_name
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $barode_code
 * @property string $spec
 * @property integer $unit_id
 * @property integer $number
 * @property string $remark
 * @property integer $update_time
 * @property integer $confirm_time
 * @property string $batch_num
 * @property integer $add_user_id
 * @property string $add_user_name
 * @property integer $create_time
 * @property integer $confirm_user_id
 * @property string $confirm_user_name
 * @property string $unit_name
 * @property integer $status
 */
class Moving extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%moving}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'goods_id', 'brand_id', 'unit_id', 'number', 'confirm_time', 'add_user_id', 'create_time', 'confirm_user_id', 'status'], 'integer', 'message'=>'必须为数字'],
            [['remark'], 'string'],
            [['from_warehouse_name', 'to_warehouse_name', 'brand_name', 'barode_code', 'spec', 'add_user_name', 'confirm_user_name', 'unit_name'], 'string', 'max' => 32],
            [['store_name', 'goods_name'], 'string', 'max' => 255],
            [['batch_num'], 'string', 'max' => 100],
            [['from_warehouse_id'], 'required', 'message' => '请选择调出仓库'],
            [['to_warehouse_id'], 'required', 'message' => '请选择调入仓库'],
            [['goods_name'], 'required', 'message' => '请选择商品中英文名称'],
            [['batch_num'], 'required', 'message' => '请选择商品的采购批号'],
            [['number'], 'required', 'message' => '调剂数量不能为空'],
            [['update_time'], 'required', 'message' => '请选择调剂日期'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'moving_id' => 'ID序号',
            'from_warehouse_id' => '来源仓库ID',
            'from_warehouse_name' => '来源仓库名称',
            'to_warehouse_id' => '目标仓库ID',
            'to_warehouse_name' => '目标仓库名称',
            'store_id' => '入驻商家ID',
            'store_name' => '入驻商家名称',
            'goods_id' => '商品ID',
            'goods_name' => '商品名称',
            'brand_id' => '品牌ID',
            'brand_name' => '品牌名称',
            'barode_code' => '条形码',
            'spec' => '规格',
            'unit_id' => '计量单位',
            'number' => '调配数量',
            'remark' => '备注',
            'update_time' => '调剂时间',
            'confirm_time' => '入库时间',
            'batch_num' => '批次号',
            'add_user_id' => '创建人ID',
            'add_user_name' => '创建人名称',
            'create_time' => '创建时间',
            'confirm_user_id' => '确认人ID',
            'confirm_user_name' => '确认入库人姓名',
            'unit_name' => '计量单位名称',
            'status' => '1 入库 0未入库',
        ];
    }
}
