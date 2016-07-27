<?php

namespace frontend\controllers;
use frontend\components\menuHelper;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\RefuseGoodsModel;
use frontend\models\StocksModel;
use Yii;
use frontend\models\RefuseModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * RefuseController implements the CRUD actions for RefuseModel model.
 */
class RefuseController extends CommonController
{
    /**
     * Lists all RefuseModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RefuseModel();
        $s_con = Yii::$app->request->queryParams;
        //搜索条件
        $refuse_time_start = strtotime(Yii::$app->request->get('refuse_time_start')); //退货入库开始时间
        $refuse_time_end = strtotime(Yii::$app->request->get('refuse_time_end').' 23:59:59'); //退货入库结束时间
        $where=array();
        if(Yii::$app->user->identity->store_id>0){
            $where['r.store_id']=Yii::$app->user->identity->store_id;
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['r.store_id'] = $sid;
            }
        }
        if(Yii::$app->user->identity->type==2)  $where['w.principal_id']=Yii::$app->user->identity->user_id;
        if(!empty($s_con['warehouse_id'])) $where['r.warehouse_id'] = $s_con['warehouse_id']; //仓库
		if(isset($s_con['status'])){
			if($s_con['status']==1) $where['r.status'] = $s_con['status'];//入库状态
			if($s_con['status']==2) $where['r.status'] = 0; //入库状态
		}
        if (!empty($s_con['shop_id'])) $where['r.shop_id'] = $s_con['shop_id']; //销售平台

        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = (new \yii\db\Query())->from($tablePrefix.'refuse_order as r')
            ->select('r.shop_name,r.warehouse_id,a.real_name,r.warehouse_name,r.order_no,r.refuse_id,r.refuse_amount,r.refuse_time,r.status,r.confirm_time,rog.unit_name')
            ->innerJoin(['rog'=>$tablePrefix.'refuse_order_goods'],'r.refuse_id=rog.refuse_id')
            ->innerJoin(['w'=>$tablePrefix.'warehouse'],'r.warehouse_id=w.warehouse_id')
			->innerJoin(['a'=>$tablePrefix.'customers'],'a.customers_id=r.customer_id')
            ->where($where)->orderBy(['r.status'=>SORT_ASC,'r.refuse_time'=>SORT_DESC]);
        if (!empty($s_con['order_no'])) $query->andFilterWhere(['like', 'r.order_no',$s_con['order_no']]); //订单编号
        if (!empty($s_con['username'])) $query->andFilterWhere(['like', 'a.username',$s_con['username']]); //客户帐号
        if (!empty($s_con['real_name'])) $query->andFilterWhere(['like', 'a.real_name',$s_con['real_name']]); //客户姓名
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'rog.goods_name',$s_con['goods_name']]); //商品名称
        if($refuse_time_start<=$refuse_time_end) {
            if(!empty($refuse_time_start)) $query->andWhere(['>=','r.refuse_time',$refuse_time_start]);
            if(!empty($refuse_time_end))   $query->andWhere(['<=','r.refuse_time',$refuse_time_end]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>10]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $goods = array();
        foreach($dataProvider as $val){
            $goods[$val['refuse_id']] = $searchModel::getRefuseGoods($val['refuse_id']);
        }
        //导出表格
        if(Yii::$app->request->get('action')=="export") {
            $final = [['销售平台', '订单编号','收货人姓名', '商品中英文名称（含规格）', '退货数量', '退款金额', '退货日期', '入库仓库', '入库状态', '入库日期']];
            foreach ($countQuery->all() as $feed) {
                $name = $nums = "";
                // 把需要处理的数据都处理一下
               
				$cgoods = $searchModel::getRefuseGoods($val['refuse_id']);
                foreach ($cgoods as $v) {
                    $name .= $v['goods_name']."\t  ".$v['spec']."\t";
                    $nums .= $v['number']."\t";
                }
				
				
                $final[] = [
                    $feed['shop_name'], $feed['order_no']."\t",$feed['accept_name']."\t", $name, $nums, $feed['refuse_amount'], $feed['refuse_time']>0 ? date('Y-m-d', $feed['refuse_time']):"　", $feed['warehouse_name'],
                    ($feed['status'] == 0) ? "否" : "是", $feed['confirm_time']>0? date('Y-m-d',$feed['confirm_time']):'　',
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'goods' =>$goods,
            'pages' => $pages,
        ]);

    }

    /**
     * Displays a single RefuseModel model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $query = new \yii\db\Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        //商品及赠品
        $goodlist = $query->select('g.name,g.spec,b.name as bname,rog.sell_price,rog.number,rog.unit_name')->from($tablePrefix.'refuse_order_goods as rog')
            ->leftJoin($tablePrefix.'goods as g','rog.goods_id=g.goods_id')
            ->leftJoin($tablePrefix.'brand as b','g.brand_id=b.brand_id')
            ->where('rog.refuse_id='.$id)->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'goodlist'  => $goodlist,
        ]);
    }

    public function actionHandle($refuse_id){
        $action = Yii::$app->request->get('action');
        if($action){
                if(!$refuse = RefuseModel::findOne($refuse_id))
                {
                    throw new NotFoundHttpException($refuse_id.'ID不存在！');
                }
                $goods = RefuseModel::getRefuseGoods($refuse_id);
                $transaction=Yii::$app->db->beginTransaction();
                try {
                    foreach($goods as $v) {
                        if ($model = StocksModel::findOne(['refuse_id' => $refuse_id, 'goods_id' => $v['goods_id']]))
                        {
                            $refuses = RefuseGoodsModel::findOne(['refuse_id' => $refuse_id, 'goods_id' => $v['goods_id']]);
                            $refuses->sbatch_num=" ";
                            if (!$model->delete() || !$refuses->save()) {
                                throw new \Exception($refuse['goods_name'] . "商品取消退货入库操作失败！");
                            }
                        }else{
                            $refuses = RefuseGoodsModel::findOne(['refuse_id' => $refuse_id, 'goods_id' => $v['goods_id']]);
                            $model = StocksModel::findOne(['stocks_id' => $refuses['stocks_id']]);
                            $model->stock_num = $model['stock_num'] - $v['number'];
                            $refuses->sbatch_num = " ";
                            $refuses->stocks_id = 0;
                            if (!$model->save() || !$refuses->save()) {
                                throw new \Exception($refuses['goods_name'] . "商品取消退货入库操作失败！");
                            }
                        }
                        menuHelper::warnStatus($v['goods_id'],$refuse->warehouse_id);
                    }
                    $refuse->status = 0;
                    $refuse->confirm_time = 0;
                    $refuse->confirm_user_id = 0;
                    $refuse->confirm_user_name = "";
                    if(!$refuse->save()){
                        throw new NotFoundHttpException("商品取消退货入库操作失败！");
                    }
                    $transaction->commit();
                }catch(Exception $e){
                    $transaction->rollBack();
                }
        }else{
            $ref = RefuseModel::findOne(['refuse_id'=>$refuse_id]);
            if(!Order::findOne(['delivery_status'=>1,'order_id'=>$ref['order_id']])){
                throw new NotFoundHttpException( "该销售订单未出库，不可进行退货入库操作");
            }
            $where = Yii::$app->request->post();
            $store_id = Yii::$app->user->identity->store_id;
            $transaction=Yii::$app->db->beginTransaction();
            try {
                foreach ($where['batch_num'] as $k => $v) {
                    if ($v) {
                        $whe['batch_num'] = $v;
                        $whe['warehouse_id'] = $where['warehouse_id'];
                        $whe['goods_id'] = $where['goods_id'][$k];
                        if ($store_id > 0) $whe['store_id'] = $store_id;
                        if($stocks = StocksModel::findOne($whe)){
                            $refuse = RefuseGoodsModel::findOne(['refuse_id' => $where['refuse_id'], 'goods_id' => $where['goods_id'][$k]]);
                            $refuse->stocks_id = $stocks['stocks_id'];
                            $refuse->sbatch_num = $v;
                            $stocks->stock_num = $stocks['stock_num'] + $where['number'][$k];
                            if ((!$stocks->save()) || (!$refuse->save())) {
                                throw new \Exception($stocks['goods_name'] . "退货入库确认操作失败！");
                            }
                        }else{
                            $val = Goods::findOne(['goods_id'=>$where['goods_id'][$k]]);
                            $stocks = new StocksModel();
                            $stocks->store_id = Yii::$app->user->identity->store_id;
                            $stocks->store_name = Yii::$app->user->identity->store_name;
                            $stocks->warehouse_id = $where['warehouse_id'];
                            $stocks->warehouse_name = $where['warehouse_name'];
                            $stocks->goods_id = $val['goods_id'];
                            $stocks->goods_name = $val['name'];
                            $stocks->brand_id = $val['brand_id'];
                            $stocks->brand_name = $val['brand_name'];
                            $stocks->barode_code = $val['barode_code'];
                            $stocks->cat_id = $val['cat_id'];
                            $stocks->cat_name = $val['cat_name'];
                            $stocks->unit_id = $val['unit_id'];
                            $stocks->unit_name = $val['unit_name'];
                            $stocks->spec = $val['spec'];
                            $stocks->stock_num = $where['number'][$k];
                            $stocks->batch_num = $v;
                            $stocks->refuse_id = $refuse_id;
                            $refuse = RefuseGoodsModel::findOne(['refuse_id' => $refuse_id, 'goods_id' =>$val['goods_id']]);
                            $refuse->sbatch_num=$v;
                            if(!$stocks->save() || !$refuse->save()){
                                throw new NotFoundHttpException($val['goods_name'] . "退货入库确认操作失败！");
                            }

                        }
                        menuHelper::warnStatus($where['goods_id'][$k],$where['warehouse_id']);
                    }else {
                        $whe['warehouse_id'] = $where['warehouse_id'];
                        $whe['goods_id'] = $where['goods_id'][$k];
                        $whe['batch_num'] = NULL;
                        if ($store_id > 0) $whe['store_id'] = $store_id;
                        if($stocks = StocksModel::findOne($whe)){
                            $refuse = RefuseGoodsModel::findOne(['refuse_id' => $where['refuse_id'], 'goods_id' => $where['goods_id'][$k]]);
                            $refuse->stocks_id = $stocks['stocks_id'];
                            $stocks->stock_num = $stocks['stock_num'] + $where['number'][$k];
                            if ((!$stocks->save()) || (!$refuse->save())) {
                                throw new NotFoundHttpException($stocks['goods_name'] . "退货入库确认操作失败！");
                            }
                            menuHelper::warnStatus($where['goods_id'][$k],$where['warehouse_id']);
                        }else{
                            $goods = RefuseModel::getRefuseGoods($where['refuse_id']);
                            foreach ($goods as $val) {
                                $stocks = new StocksModel();
                                $stocks->store_id = Yii::$app->user->identity->store_id;
                                $stocks->store_name = Yii::$app->user->identity->store_name;
                                $stocks->warehouse_id = $where['warehouse_id'];
                                $stocks->warehouse_name = $where['warehouse_name'];
                                $stocks->goods_id = $val['goods_id'];
                                $stocks->goods_name = $val['goods_name'];
                                $stocks->brand_id = $val['brand_id'];
                                $stocks->brand_name = $val['brand_name'];
                                $bar = Goods::findOne(['goods_id'=>$val['goods_id']]);
                                $stocks->barode_code = $bar['barode_code'];
                                $stocks->cat_id = $bar['cat_id'];
                                $stocks->cat_name = $bar['cat_name'];
                                $stocks->unit_id = $bar['unit_id'];
                                $stocks->unit_name = $bar['unit_name'];
                                $stocks->spec = $bar['spec'];
                                $stocks->stock_num = $val['number'];
                                $stocks->batch_num = '';
                                $stocks->refuse_id = $refuse_id;
                                if (!$stocks->save()) {
                                    throw new NotFoundHttpException($val['goods_name'] . "退货入库失败！");
                                }
                                menuHelper::warnStatus($val['goods_id'],$where['warehouse_id']);
                            }
                        }
                    }
                }
                $refuse = RefuseModel::find()->where(['refuse_id' => $where['refuse_id']])->one();
                $refuse->status = 1;
                $refuse->confirm_time = time();
                $refuse->confirm_user_id = Yii::$app->user->identity->id;
                $refuse->confirm_user_name = Yii::$app->user->identity->real_name;
                if(!$refuse->save()){
                    throw new NotFoundHttpException("退货入库失败！");
                }
                $transaction->commit();
            }catch(Exception $e){
                $transaction->rollBack();
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Finds the RefuseModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RefuseModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RefuseModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('请求的页面不存在');
        }
    }
}
