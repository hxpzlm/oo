<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6
 * Time: 14:48
 */
namespace frontend\components;
use common\models\User;
use frontend\models\StocksModel;
use frontend\models\WarehouseModel;
use frontend\models\Warning;
use frontend\models\WarningInfo;
use Yii;
use yii\caching\TagDependency;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Expression;
class menuHelper{

/*
*获取权限菜单
* @return array
*/
    /**
     * @param $userId
     * @param null $root
     * @param null $callback
     */
    public static function getAssignmentMenu($parent){
        if (empty($parent)) {
            return [];
        }
        $db = new Query();
        $menu=$db->select('name,menu_name,parent')->from(Yii::$app->db->tablePrefix.'auth_item')->where(['parent'=>$parent,'type'=>2,'is_display'=>1])->orderBy(['sort'=>SORT_ASC])->all();
        return $menu;
    }

    public static function getRefuseData(){
        $query = new Query();
        $where['r.status']= 0;
        $user = Yii::$app->user->identity;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = $query->select(['r.warehouse_id','r.warehouse_name','r.refuse_id','r.order_no','r.shop_name','r.refuse_amount'])->from($tablePrefix."refuse_order as r")
            ->leftJoin(['w'=>$tablePrefix.'warehouse'],'w.warehouse_id=r.warehouse_id')
            ->where($where)->orderBy(['r.create_time'=>SORT_DESC]);
        if($user->store_id>0){
            $query->andWhere(['=','r.store_id',$user->store_id]);
            if(Yii::$app->user->identity->type!=1){
                $query->andWhere(['=','w.principal_id',Yii::$app->user->id]);
            }
        }
        $msg = $query->count();
        $data = $query->limit(5)->all();
        return array('msg'=>$msg,'data'=>$data);
    }
    public static  function getAuthData(){
        $query = new Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->type==1 && Yii::$app->user->identity->store_id==0){
            return $query->select(['name','menu_name','parent','is_display'])
                ->from($tablePrefix.'auth_item')
                ->where(['type'=>2])
                ->andFilterWhere(['!=','menu_name','首页'])
                ->orderBy(['sort'=>SORT_ASC])
                ->all();
        }else{
            return $query->select(['name','menu_name','parent','is_display'])
                ->from($tablePrefix.'auth_item')
                ->where(['type'=>2])
                ->andFilterWhere(['!=','menu_name','首页'])
                ->andFilterWhere(['not like','name','store'])
                ->orderBy(['sort'=>SORT_ASC])
                ->all();
        }


    }
    //销售出库数据（未出库）
    public static function getOrderData(){
        $query = new Query();
        $where['o.delivery_status']= 0;
        $user = Yii::$app->user->identity;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = $query->select(['o.order_id','o.order_no','o.shop_name','o.real_pay'])->from($tablePrefix."order as o")->where($where)->orderBy(['o.create_time'=>SORT_DESC])
            ->innerJoin(['w'=>$tablePrefix.'warehouse'],"w.warehouse_id=o.warehouse_id");
        if($user->store_id>0){
            $query->andWhere(['=','o.store_id',$user->store_id]);
            if(Yii::$app->user->identity->type!=1){
                $query->andWhere(['=','w.principal_id',Yii::$app->user->id]);
            }
        }

        $msg = $query->count();
        $data = $query->limit(5)->all();
        return array('msg'=>$msg,'data'=>$data);
    }
    //获取仓库销售量

    public static function getSale(){
        $query = new Query();
        $arr= array();
        $user = Yii::$app->user->identity;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if($user->store_id>0){
            $ware = WarehouseModel::find()->where(['status'=>1,'store_id'=>$user->store_id])->orderBy(['name'=>SORT_ASC])->all();
            $where['store_id']= $user->store_id;
            foreach($ware as $k=>$v){
                $arr[$k]['name'] = $v['name'];
                $where['name'] = $v['name'];
                $query->from($tablePrefix.'salenums')->select('smonth,sale_nums')->where($where);
                $arr[$k]['data'] = $query->andFilterWhere(['=','syear',date('Y',time())])->all();
            }
            return $arr;
        }

    }

    public static function getSales(){
        $query = new Query();
        $arr= array();
        $user = Yii::$app->user->identity;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if($user->store_id>0){
            $query->from($tablePrefix.'sale as s')->innerJoin(['w'=>$tablePrefix.'warehouse'],'w.warehouse_id=s.warehouse_id')
                ->where(['s.year'=>date('Y',time()),'s.store_id'=>$user->store_id]);
            $cou = clone $query;
            $arr['data']=$query->addSelect('w.warehouse_id,w.name,sale_nums')->orderBy(['sale_nums'=>SORT_DESC])->all();
            $arr['count']=$cou->sum('s.sale_nums');
            return $arr;
        }
    }

   //获取采购和库存
    public static function getPGoodsData(){
        $query = new Query();
        $where['p.purchases_status']= 0;
        $user = Yii::$app->user->identity;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = $query->select(['g.spec','g.purchase_id','g.goods_name','g.brand_name','g.supplier_name','p.totle_price'])->from($tablePrefix."purchase as p")
            ->innerJoin(['g'=>$tablePrefix.'purchase_goods'],'g.purchase_id=p.purchase_id')->leftJoin(['w'=>$tablePrefix.'warehouse'],'w.warehouse_id=p.warehouse_id')
            ->where($where)->orderBy(['p.create_time'=>SORT_DESC]);
        if($user->store_id>0){
            $query->andWhere(['=','p.store_id',$user->store_id]);
            if(Yii::$app->user->identity->type!=1){
                $query->andWhere(['=','w.principal_id',Yii::$app->user->id]);
            }
        }
        $msg = $query->count();
        $data = $query->limit(5)->all();
        return array('msg'=>$msg,'data'=>$data);
    }
    //获取销售和库存
    public static function getSaleNums(){
        $query = new Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['store_id'] = Yii::$app->user->identity->store_id;
            $sw['store_id'] = Yii::$app->user->identity->store_id;
        }
        $where['status'] =1;
        $ware = $query->select('name')->from($tablePrefix.'warehouse')->where($where)->all();
        foreach($ware as $k=>$v){
            $sw['name']=$v['name'];
            $ware[$k]['totle'] = $query->select('sdate,sale_nums')->from($tablePrefix."salenums")
                ->where($sw)->orderBy(['name'=>SORT_DESC])->all();
        }
        return self::sortNums($ware);
    }

    public static  function sortNums($sortData){
        foreach($sortData as $k=>$v){
            $arr = [];
            foreach($v['totle'] as $val){
                $k = date('m',strtotime($val['sdate']));
                $arr[$k]=$val['sale_nums'];
            }
            $sortData[$k]['totle'] = $arr;
        }
        return $sortData;
    }

    public static function getStocksPurchase(){
        $query = new Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['store_id'] = Yii::$app->user->identity->store_id;
            $data = $query->select('warehouse_id,warehouse_name as name,purchase_totle as p_totle,stocks_totle as t_totle')
                ->from($tablePrefix.'stock_purchase')->where($where)->orderBy(['name'=>SORT_ASC])->all();
            return $data;
        }

    }

    public static function getRefuse(){
        $query = new Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['store_id'] = Yii::$app->user->identity->store_id;
            $data = $query->select('warehouse_id,warehouse_name as name,nums')
                ->from($tablePrefix.'refuse_totle')->where($where)->orderBy(['name'=>SORT_ASC])->all();
            return $data;
        }

    }

    public static  function getSaleStocks(){
        $query = new Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['s.store_id'] = Yii::$app->user->identity->store_id;
            $data = $query->select('s.warehouse_name as name,s.stocks_totle as t_totle,o.sale_nums as totle')
                ->from($tablePrefix.'stock_purchase as s')
                ->innerJoin(['o'=>$tablePrefix.'sale'],'o.warehouse_id=s.warehouse_id')
                ->where($where)->orderBy(['name'=>SORT_ASC])->all();
            return $data;
        }

    }

    public static function warningInfo($goods_id,$warehouse_id){
        $warn = Warning::findOne(['goods_id'=>$goods_id,'warehouse_id'=>$warehouse_id,'is_warning'=>1]);
        if($warn){
            $totle=StocksModel::find()->where(['goods_id'=>$goods_id,'warehouse_id'=>$warehouse_id])->sum('stock_num');
                if($totle<=$warn->warning_num){
                    $udata = User::find()->select('mobile')->asArray()->where(['user_id'=>$warn->princial_id])->one();
                    $model = new WarningInfo();
                    $model->info=$info="库存不足：".$warn->goods_name."　".$warn->spec."（".$warn->warehouse_name."中库存数量：".$totle."件） ！ ";
                    $model->goods_name = $warn->goods_name;
                    $model->goods_id = $warn->goods_id;
                    $model->warehouse_id = $warn->warehouse_id;
                    $model->store_id = Yii::$app->user->identity->store_id;
                    $model->store_name = Yii::$app->user->identity->store_name;
                    $model->warning_num = $warn->warning_num;
                    $model->warning_time = time();
                    $model->close_type =0;
                    $obj = new HuyiSms();
                    $status = $obj->send($udata['mobile'],$info);
                    if($status['code']==2)
                        $model->is_send=1;
                    else
                        $model->is_send=0;
                    $model->save();
                }
        }
        return true;
    }

    public static function warnStatus($goods_id,$warehouse_id){
         if($model = WarningInfo::find()->where(['goods_id'=>$goods_id,'warehouse_id'=>$warehouse_id,'close_type'=>0])->orderBy('id desc')->one()){
               $totle=StocksModel::find()->where(['goods_id'=>$goods_id,'warehouse_id'=>$warehouse_id])->sum('stock_num');
               if($totle>$model->warning_num)
                 WarningInfo::updateAll(['close_type'=>1,'close_time'=>time()],'goods_id=:id and warehouse_id=:wid and close_type=:type',[':id'=>$goods_id,':wid'=>$warehouse_id,':type'=>0]);
               else
                 self::warningInfo($goods_id,$warehouse_id);
         }
    }










}
