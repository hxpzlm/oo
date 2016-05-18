<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $goods_id
 * @property string $name
 * @property integer $store_id
 * @property string $store_name
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $barode_code
 * @property string $spec
 * @property integer $cat_id
 * @property string $cat_name
 * @property double $weight
 * @property double $volume
 * @property integer $shelf_life
 * @property integer $unit_id
 * @property string $unit_name
 * @property string $intro
 * @property string $virtue
 * @property string $painter
 * @property string $suggest
 * @property string $element
 * @property string $store_mode
 * @property integer $create_time
 * @property integer $principal_id
 * @property string $principal_name
 * @property integer $sort
 * @property integer $add_user_id
 * @property string $add_user_name
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'brand_id', 'cat_id', 'shelf_life', 'unit_id', 'create_time', 'principal_id', 'sort', 'add_user_id'], 'integer','message'=>'必须为数字'],
            [['weight', 'volume'], 'number'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 200],
            [['store_name', 'brand_name', 'barode_code', 'cat_name'], 'string', 'max' => 32],
            [['spec', 'principal_name', 'add_user_name'], 'string', 'max' => 20],
            [['unit_name'], 'string', 'max' => 50],
            [['virtue', 'painter', 'suggest', 'element'], 'string', 'max' => 255],
            [['store_mode'], 'string', 'max' => 100],
            [['name'],'unique','message' => '商品名称已存在'],
            [['name','spec','brand_id','unit_name','barode_code','weight','volume','shelf_life','cat_id','sort','principal_id'], 'required', 'message' => '不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()

    {
        return [
            'goods_id' => Yii::t('app', ''),//商品ID
            'name' => Yii::t('app', ''),//商品名称
            'store_id' => Yii::t('app', ''),//入驻商家ID
            'store_name' => Yii::t('app', ''),//入驻商家名称
            'brand_id' => Yii::t('app', ''),//品牌ID
            'brand_name' => Yii::t('app', ''),//品牌名称
            'barode_code' => Yii::t('app', ''),//条形码
            'spec' => Yii::t('app', ''),//商品规格
            'cat_id' => Yii::t('app', ''),//商品分类
            'cat_name' => Yii::t('app', ''),//分类名称
            'weight' => Yii::t('app', ''),//净重
            'volume' => Yii::t('app', ''),//体积
            'shelf_life' => Yii::t('app', ''),//保质期
            'unit_id' => Yii::t('app', ''),//计量单位ID
            'unit_name' => Yii::t('app', ''),//计量单位名称
            'intro' => Yii::t('app', ''),//商品介绍
            'virtue' => Yii::t('app', ''),//功效
            'painter' => Yii::t('app', ''),//适用人群
            'suggest' => Yii::t('app', ''),//服用方法
            'element' => Yii::t('app', ''),//主要成分
            'store_mode' => Yii::t('app', ''),//储存方式
            'create_time' => Yii::t('app', ''),//创建时间
            'principal_id' => Yii::t('app', ''),//负责人
            'principal_name' => Yii::t('app', ''),//负责人姓名
            'sort' => Yii::t('app', ''),//排序
            'add_user_id' => Yii::t('app', ''),//创建人ID
            'add_user_name' => Yii::t('app', ''),//创建人名称
        ];
    }

    public static function GetCategory($pid,$cat_id, $pad=''){

        $store_id= Yii::$app->user->identity->store_id;
        $where=array();
        $where['parent_id']=$pid;
        if($store_id>0){
            $where['store_id']=$store_id;
        }
        $option='';
        $getcat= Category::find()->select('cat_id,name')->where($where)->orderBy(['sort'=>SORT_ASC,'cat_id'=>SORT_DESC])->all();
        foreach($getcat as $v){
            //判断有没有选中
            $select = $v['cat_id'] == $cat_id ? 'selected="selected"' : '';
            $option.="<option  value='{$v['cat_id']}' $select>$pad{$v['name']}</option>";
            $option.= self::GetCategory($v['cat_id'],$cat_id,$pad.'&nbsp;&nbsp;');
        }
        return $option;
    }

    public static function GetCategory_name($id, $pad=''){

        $store_id= Yii::$app->user->identity->store_id;
        $where=array();
        $where['cat_id']=$id;
        if($store_id>0){
            $where['store_id']=$store_id;
        }
        $option='';
        $getcat= Category::find()->select('parent_id')->where($where)->orderBy(['sort'=>SORT_ASC])->all();
        foreach($getcat as $v){
            if($v['parent_id']==0){
                $option=$pad;
            }else{
                $option= self::GetCategory_name($v['parent_id'],$pad.'-');
            }

        }
        return $option;
    }

}
