<?php

namespace frontend\models;

use common\models\User;
use yii\db\Query;
use frontend\models\StocksModel;
use Yii;

/**
 * This is the model class for table "{{%warning}}".
 *
 * @property integer $warning_id
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $spec
 * @property integer $warning_num
 * @property integer $is_warning
 * @property integer $princial_id
 * @property string $princial_name
 * @property integer $create_time
 * @property integer $create_user_id
 * @property string $create_user_name
 * @property integer $modify_time
 * @property integer $modify_user_id
 * @property string $modify_user_name
 */
class Warning extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%warning}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'goods_id', 'warning_num', 'is_warning','store_id', 'princial_id', 'create_time', 'create_user_id', 'modify_time', 'modify_user_id'], 'integer'],
            [['warehouse_name', 'goods_name','store_name', 'create_user_name'], 'required'],
            [['warehouse_name', 'goods_name','store_name'], 'string', 'max' => 100],
            [['spec','create_user_name', 'modify_user_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'warning_id' => 'Warning ID',
            'warehouse_id' => 'Warehouse ID',
            'warehouse_name' => 'Warehouse Name',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'spec' => 'Spec',
            'warning_num' => 'Warning Num',
            'is_warning' => 'Is Warning',
            'store_id' => 'store_id ID',
            'store_id_name' => 'store Name',
            'princial_id' => 'Princial ID',
            'create_time' => 'Create Time',
            'create_user_id' => 'Create User ID',
            'create_user_name' => 'Create User Name',
            'modify_time' => 'Modify Time',
            'modify_user_id' => 'Modify User ID',
            'modify_user_name' => 'Modify User Name',
        ];
    }
    /*
     * $ware_id  int  仓库ID
     * $store_id  int 所属商家ID
     * return Array */
    public static function getWarning($ware_id,$store_id){
        $where['s.warehouse_id'] = $ware_id;
        $wh['status']=1;
        if($store_id>0) $wh['store_id']=$where['s.store_id']=$store_id;
        $data['goods']=(new Query())->from(StocksModel::tableName().'as s')
            ->select('s.goods_id,s.goods_name,s.spec,s.warehouse_name,w.princial_id,w.is_warning,w.warning_num')
            ->leftJoin(['w'=>self::tableName()],'w.goods_id=s.goods_id and w.warehouse_id=s.warehouse_id')
            ->where($where)->groupBy('s.goods_id')->orderBy('s.goods_id desc')->all();
        $data['user'] = \common\models\User::find()->select('user_id,real_name')->where($wh)->orderBy('user_id desc')->asArray()->all();
        Yii::$app->response->format=Response::FORMAT_JSON;
        return $data;
    }
}
