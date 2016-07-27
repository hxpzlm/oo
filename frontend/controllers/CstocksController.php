<?php
/**
 * Created by PhpStorm. 采购入库
 * User: Administrator
 * Date: 2016/4/14
 * Time: 13:40
 */
namespace frontend\controllers;
use frontend\components\menuHelper;
use frontend\models\Brand;
use frontend\models\OrderGoods;
use frontend\models\Purchase;
use frontend\models\PurchaseGoods;
use frontend\models\StocksModel;
use frontend\models\Suppliers;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class CstocksController extends CommonController{

    public function actionIndex(){
        $s_con = Yii::$app->request->queryParams;
        //搜索条件
        $where=array();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['p.store_id']=Yii::$app->user->identity->store_id;
            if(Yii::$app->user->identity->type!=1){
                $where['w.principal_id']=Yii::$app->user->id;
            }
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['p.store_id'] = $sid;
            }
        }
        if (!empty($s_con['warehouse_id'])) $where['p.warehouse_id'] = $s_con['warehouse_id']; //仓库
        if (!empty($s_con['supplier_name'])) $where['g.supplier_name'] = $s_con['supplier_name']; //供应商
		if(isset($s_con['purchases_status'])){
			if($s_con['purchases_status']==1) $where['p.purchases_status'] = $s_con['purchases_status']; //入库状态
			if($s_con['purchases_status']==2) $where['p.purchases_status'] = 0; //入库状态
		}
        $buy_time_start = strtotime(Yii::$app->request->get('buy_time_start')); //采购入库开始时间
        $buy_time_end = strtotime(Yii::$app->request->get('buy_time_end').' 23:59:59'); //采购退货入库结束时间
		
        $query = (new \yii\db\Query())->from($tablePrefix.'purchase as p')
            ->select('g.*,p.warehouse_name,p.totle_price,p.buy_time,p.purchases_status,p.purchases_time')
            ->innerJoin(['g'=>$tablePrefix.'purchase_goods'],'g.purchase_id=p.purchase_id')
            ->innerJoin(['w'=>$tablePrefix.'warehouse'],'w.warehouse_id=p.warehouse_id')
            ->where($where)->orderBy(['p.purchases_status'=>SORT_ASC,'p.buy_time'=>SORT_DESC]);
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'g.goods_name',html_entity_decode($s_con['goods_name'])]); //商品名称
        if (!empty($s_con['barode_code'])) $query->andFilterWhere(['like', 'g.barode_code',$s_con['barode_code']]); //条形码
        if (!empty($s_con['brand_name'])) $query->andFilterWhere(['like', 'g.brand_name',html_entity_decode($s_con['brand_name'])]); //品牌
        if($buy_time_start<=$buy_time_end) {
            if(!empty($buy_time_start)) $query->andWhere(['>=','p.buy_time',$buy_time_start]);
            if(!empty($buy_time_end))   $query->andWhere(['<=','p.buy_time',$buy_time_end]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        //导出表格
        if(Yii::$app->request->get('action')=="export") {
            $final = [['仓库', '商品中英文名称（含规格）', '品牌', '采购单价', '采购数量', '总价', '采购日期', '供应商','入库状态', '入库日期']];
            foreach ($countQuery->all() as $feed) {
                // 把需要处理的数据都处理一下
                $final[] = [
                    $feed['warehouse_name'], $feed['goods_name']."　　".$feed['spec'], $feed['brand_name'], $feed['buy_price']."\t", $feed['number'],
                    $feed['totle_price']."\t",($feed['buy_time']>0)?date("Y-m-d",$feed['buy_time']):"", $feed['supplier_name'],($feed['purchases_status']==0)?"否":"是",($feed['purchases_time']>0)?date("Y-m-d",$feed['purchases_time']):""
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }
        $wh['status'] = 1;
        if(Yii::$app->user->identity->store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }
        $brand = Brand::findAll($wh);
        $supplier = Suppliers::findAll($wh);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'brand'        => $brand,
            'supplier'    => $supplier,
            'pages' => $pages,
        ]);
    }
    //确认采购入库
    public function actionHandle($id,$action=""){

        $data = Purchase::findOne($id);
        if(!$data) throw new NotFoundHttpException($id."不存在");
        if($action=="comfirm"){
            $transaction=Yii::$app->db->beginTransaction();
            try{
                $goods = PurchaseGoods::findAll(['purchase_id'=>$id]);
                foreach($goods as $v){
                    $model = new StocksModel();
                    $cat = Purchase::getGoodsCat($v['goods_id']);
                    $model->store_id = $data->store_id;
                    $model->store_name = $data->store_name;
                    $model->warehouse_id = $data->warehouse_id;
                    $model->warehouse_name = $data->warehouse_name;
                    $model->goods_id = $v['goods_id'];
                    $model->goods_name = $v['goods_name'];
                    $model->brand_id = $v['brand_id'];
                    $model->brand_name = $v['brand_name'];
                    $model->barode_code = $v['barode_code'];
                    $model->spec = $v['spec'];
                    $model->cat_id = $cat['cat_id'];
                    $model->cat_name = $cat['cat_name'];
                    $model->unit_id = $v['unit_id'];
                    $model->unit_name = $v['unit_name'];
                    $model->batch_num = $data->batch_num;
                    $model->purchase_id = $data->purchase_id;
                    $model->purchase_num = $v['number'];
                    $model->purchase_time = $data->buy_time;
                    $model->stock_num = $v['number'];
                    if(!$model->save()){
                        throw new NotFoundHttpException($v['goods_name']."商品入库失败！");
                    }
                   menuHelper::warnStatus($v['goods_id'],$data->warehouse_id);
                }
                $data->purchases_status= 1;
                $data->purchases_time = time();
                $data->confirm_user_id = Yii::$app->user->id;
                $data->confirm_time=time();
                $data->confirm_user_name = Yii::$app->user->identity->real_name;
                if(!$data->save()){
                    throw new NotFoundHttpException("采购入库操作失败！");
                }
                $transaction->commit();
            } catch(Exception $e){
                $transaction->rollBack();
            }
        }
        if($action=="cancle"){
            $transaction=Yii::$app->db->beginTransaction();
            try{
                $stocks = StocksModel::findOne(['purchase_id'=>$id]);
                if(OrderGoods::findAll(['stocks_id'=>$stocks['stocks_id']])) throw  new NotFoundHttpException($stocks['goods_name']."已经有商品出库，不能取消入库");
                $data->purchases_status= 0;
                $data->purchases_time = 0;
                $data->confirm_user_id = 0;
                $data->confirm_time=0;
                $data->confirm_user_name = "";
                if((!StocksModel::deleteAll(['purchase_id'=>$id]))|| (!$data->save())){
                    throw new NotFoundHttpException('取消入库操作失败！');
                }
                menuHelper::warnStatus($stocks->goods_id,$data->warehouse_id);
                $transaction->commit();
            } catch(Exception $e){
                $transaction->rollBack();
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }
	
	public function actionView($id)
    {
        return $this->render('view', [
            'model' => Purchase::findOne(['purchase_id'=>$id]),
            'model_pg'  => PurchaseGoods::findOne(['purchase_id'=>$id]),
        ]);
    }


}