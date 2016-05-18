<?php
/**
 * Created by xiegao  销售出库.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 14:18
 */
namespace frontend\controllers;
use frontend\models\OrderGoods;
use frontend\models\RefuseModel;
use frontend\models\StocksModel;
use Yii;
use yii\helpers\Url;
use frontend\models\OrderModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

class SaleController extends CommonController
{

    public function actionComfirm(){
        $s_con = Yii::$app->request->queryParams;
        $order_time_start = empty(Yii::$app->request->get('order_time_start'))? 0:strtotime(Yii::$app->request->get('order_time_start')); //销售出库开始时间
        $order_time_end = empty(Yii::$app->request->get('order_time_start'))? time():strtotime(Yii::$app->request->get('order_time_end')); //销售出库结束时间
        $where=array();
        if(Yii::$app->user->identity->store_id>0){
            $where['o.store_id']=Yii::$app->user->identity->store_id;
            if(Yii::$app->user->identity->type!=1){
                $where['w.principal_id']=Yii::$app->user->id;
            }
        }
        if(!empty($s_con['warehouse_id'])) $where['o.warehouse_id'] = $s_con['warehouse_id']; //仓库
		if(isset($s_con['status'])){
			if($s_con['status']==1)  $where['o.delivery_status'] = $s_con['status']; //入库状态
			if($s_con['status']==2)  $where['o.delivery_status'] = 0; //入库状态
		}
        if (!empty($s_con['shop_id'])) $where['o.shop_id'] = $s_con['shop_id']; //销售平台
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = (new \yii\db\Query())->from($tablePrefix.'order as o')
            ->select('o.shop_name,o.warehouse_name,o.order_no,o.real_pay,o.sale_time,o.customer_name,o.order_id,o.confirm_time,o.delivery_status,o.delivery_code')
            ->leftJoin(['og'=>$tablePrefix.'order_goods'],'og.order_id=o.order_id')
			->leftJoin(['g'=>$tablePrefix.'goods'],'g.goods_id=og.goods_id')
            ->leftJoin(['w'=>$tablePrefix.'warehouse'],'o.warehouse_id=w.warehouse_id')
            ->where($where)->orderBy(['o.delivery_status'=>SORT_ASC,'o.sale_time'=>SORT_DESC])->groupBy(['order_id']);
        if (!empty($s_con['order_no'])) $query->andFilterWhere(['like', 'o.order_no',$s_con['order_no']]); //订单编号
        if (!empty($s_con['customer_name'])) $query->andFilterWhere(['like', 'o.customer_name',$s_con['customer_name']]); //客户姓名
        if (!empty($s_con['barode_code'])) $query->andFilterWhere(['like', 'g.barode_code',$s_con['barode_code']]);
        if($order_time_start<=$order_time_end) {
            if(!empty($order_time_start)) $query->andWhere(['>=','o.confirm_time',$order_time_start]);
            if(!empty($order_time_end))   $query->andWhere(['<=','o.confirm_time',$order_time_end]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>10]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $goods = array();
        foreach($dataProvider as $val){
            $goods[$val['order_id']] = OrderModel::getOrderGoods($val['order_id']);
        }
        //导出表格
        if(Yii::$app->request->get('action')=="export") {
            $final = [['仓库名','销售平台', '订单编号', '商品中英文名称（含规格）', '商品数量', '实收款', '销售日期','出库状态', '出库日期','物流单号']];
            foreach ($query->all() as $feed) {
                $name = $nums = "";
                // 把需要处理的数据都处理一下
                foreach ($goods[$feed['order_id']] as $v) {
                    $name .= $v['goods_name'] . "  " . $v['spec'] . "\t";
                    $nums .= $v['number'] . "  \t";
                }
                $final[] = [
                    $feed['warehouse_name'], $feed['shop_name'], "'".$feed['order_no']."'", $name, $nums, $feed['real_pay'],($feed['sale_time']>0)?date('Y-m-d', $feed['sale_time']):"",
                    ($feed['delivery_status'] == 0) ? "否" : "是",($feed['confirm_time']>0)? date('Y-m-d', $feed['confirm_time']):"",$feed['delivery_code']
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }
        return $this->render('saleok', [
            'dataProvider' => $dataProvider,
            'goods' =>$goods,
            'pages' => $pages,
        ]);
    }

    public function actionHandle($order_id){
        $action = Yii::$app->request->get('action');
        if($action=="confirm"){
            $data = Yii::$app->request->post();
            if($data['delivery_code'] && $data['delivery_id'] && $data['order_id']){
                $model = OrderModel::findOne(['order_id'=>$data['order_id']]);
                if(RefuseModel::findOne(['order_no'=>$model['order_no']])){
                    throw new NotFoundHttpException("该订单已退货入库，不能确认出库！");
                }
                $goods = OrderGoods::findAll(['order_id'=>$data['order_id']]);
                $transaction=Yii::$app->db->beginTransaction();
                try{
                    foreach ($goods as $v) {
                        $stocks = StocksModel::findOne(['stocks_id'=>$v['stocks_id']]);
                        if($stocks['stock_num']>=$v['number']){
                            $stocks->stock_num = $stocks['stock_num']-$v['number'];
                            $stocks->save();
                        }else{
                            throw new NotFoundHttpException($v['goods_name']."库存不足，请先补充库存");
                        }
                    }
                    $transaction->commit();
                } catch(Exception $e){
                    $transaction->rollBack();
                }
                //$ew = ;
                $model->delivery_code = $data['delivery_code'];
                //$model->delivery_name = $ew['name'];
                $model->delivery_id = $data['delivery_id'];
                $model->confirm_user_name = Yii::$app->user->identity->real_name;
                $model->confirm_user_id = Yii::$app->user->id;
                $model->confirm_time = time();
                $model->delivery_status = 1;
                $model->save();
                return $this->redirect(Yii::$app->request->getReferrer());
            }else{
                throw new NotFoundHttpException("发送的数据异常！");
            }
        }elseif($action=="cancle"){
            $model = OrderModel::findOne(['order_id'=>$order_id]);
            if($model){
                $model->delivery_status = 0;
                $model->delivery_code = "";
                $model->delivery_name = "";
                $model->delivery_id = 0;
                $model->confirm_user_name = "";
                $model->confirm_user_id = 0;
                $model->confirm_time = 0;
                $model->save();
                return $this->redirect(Yii::$app->request->getReferrer());
            }else{
                throw new NotFoundHttpException("该订单ID不存在");
            }
        }
    }
}