<?php
/**
 * Created by xiegao  销售出库.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 14:18
 */
namespace frontend\controllers;
use frontend\components\menuHelper;
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
        $order_time_start = strtotime(Yii::$app->request->get('order_time_start')); //销售出库开始时间
        $order_time_end = strtotime(Yii::$app->request->get('order_time_end').' 23:59:59'); //销售出库结束时间
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
            ->select('o.shop_name,o.warehouse_name,o.order_no,o.real_pay,o.sale_time,a.accept_name,o.order_id,o.confirm_time,o.delivery_status,o.delivery_code,o.delivery_id')
            ->innerJoin(['og'=>$tablePrefix.'order_goods'],'og.order_id=o.order_id')
			->innerJoin(['g'=>$tablePrefix.'goods'],'g.goods_id=og.goods_id')
            ->innerJoin(['w'=>$tablePrefix.'warehouse'],'o.warehouse_id=w.warehouse_id')
			->innerJoin(['a'=>$tablePrefix.'address'],'a.address_id=o.address_id')
            ->where($where)->orderBy(['o.delivery_status'=>SORT_ASC,'o.order_id'=>SORT_DESC])->groupBy(['order_id']);
        if (!empty($s_con['order_no'])) $query->andFilterWhere(['like', 'o.order_no',$s_con['order_no']]); //订单编号
        if (!empty($s_con['accept_name'])) $query->andFilterWhere(['like', 'a.accept_name',$s_con['accept_name']]); //客户姓名
        if (!empty($s_con['delivery_code'])) $query->andFilterWhere(['like', 'o.delivery_code',$s_con['delivery_code']]); //物流单号
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'og.goods_name',$s_con['goods_name']]); //物流单号
        if($order_time_start<=$order_time_end) {
            if(!empty($order_time_start)) $query->andWhere(['>=','o.sale_time',$order_time_start]);
            if(!empty($order_time_end))   $query->andWhere(['<=','o.sale_time',$order_time_end]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $goods = array();
        foreach($dataProvider as $val){
            $goods[$val['order_id']] = OrderModel::getOrderGoods($val['order_id']);
        }
		
        //导出表格
        if(Yii::$app->request->get('action')=="export") {
            $final = [['仓库名','销售平台', '订单编号','收货人姓名', '商品中英文名称（含规格）', '商品数量', '实收款', '销售日期','出库状态', '出库日期','物流单号']];
            
			foreach ($countQuery->all() as $feed) {
                $name = $nums = "";
                // 把需要处理的数据都处理一下
	
                $cgoods = OrderModel::getOrderGoods($feed['order_id']);
                /* foreach ($cgoods as $v) {
                    $name .= $v['goods_name']."\t  ".$v['spec']."\t\r\n";
                    $nums .= $v['number']."\t\r\n";
                } */
				foreach ($cgoods as $v) {
                    $name[]= $v['goods_name']."\t  ".$v['spec']."\t";
                    $nums[]= $v['number']."\t";
                }
                $final[] = [
                    $feed['warehouse_name'], $feed['shop_name'], $feed['order_no']."\t", $feed['accept_name']."\t",$name, $nums,$feed['real_pay']."\t",($feed['sale_time']>0)?date('Y-m-d', $feed['sale_time']):"",
                    ($feed['delivery_status'] == 0) ? "否" : "是",($feed['confirm_time']>0)? date('Y-m-d', $feed['confirm_time']):"　",$feed['delivery_code']."\t"];
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
                $goods = OrderGoods::findAll(['order_id'=>$data['order_id']]);
                $transaction=Yii::$app->db->beginTransaction();
                try{
                    foreach ($goods as $v) {
                        $stocks = StocksModel::findOne(['stocks_id'=>$v['stocks_id']]);
                        if($stocks['stock_num']>=$v['number']){
                            $stocks->stock_num = $stocks['stock_num']-$v['number'];
                            $stocks->save();
                            menuHelper::warnStatus($v['goods_id'],$model->warehouse_id);
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
            if(RefuseModel::findOne(['order_id'=>$order_id,'status'=>1])){
                throw new NotFoundHttpException("该订单已退货入库，不能取消出库");
            }
            $model = OrderModel::findOne(['order_id'=>$order_id]);
            if($model){
				$goods = OrderGoods::findAll(['order_id'=>$order_id]);
				foreach($goods as $val){
					$stocks = StocksModel::findOne(['stocks_id'=>$val['stocks_id']]);
					$stocks->stock_num=$stocks['stock_num']+$val['number'];
					$stocks->save();
                    menuHelper::warnStatus($val['goods_id'],$model->warehouse_id);
				}
                $model->delivery_status = 0;
                $model->delivery_code = "";
                $model->delivery_name = "";
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

    public function actionLoad()
    {
        if (Yii::$app->getRequest()->isPost) {

            $filename = isset($_FILES['orderfile']['name'])?$_FILES['orderfile']['name']:'';
            if(empty($filename)){
                $res = array('status'=>1, 'msg'=>'请选择上传文件');
                die(json_encode($res));
            }
            $path_parts = pathinfo($filename);
            $ext = array('xls','xlsx');
            if ((strpos($_FILES["orderfile"]['type'],'application/octet-stream') === false) || !in_array(strtolower($path_parts['extension']),$ext)) {
                $res = array('status'=>2, 'msg'=>'上传文件格式不对');
                die(json_encode($res));
            }
            //限制文件大小在2M以内
            $size = 2 * 1024 * 1024;
            if (!$_FILES["orderfile"]['size'] || $_FILES["orderfile"]['size'] > $size) {
                $res = array('status'=>3, 'msg'=>'上传文件大小不能超过2M');
                die(json_encode($res));
            }

            $fileName = 'feed/'.date("YmdHis") . '.xls';
            // 自定义方法保存文件
            $tmpname = $_FILES['orderfile']['tmp_name'];
            move_uploaded_file($tmpname, $fileName);

            //从excel中读取所有信息
            // 这里说明一下，我们踩过的坑，对时间格式，正常的2016-03-03是几乎没有问题的，可就是有人要输入03/03/16等一系列时间格式
            $result = \frontend\extend\PHPExcel\Excel::getInstance()->readSheet($fileName, 0);
            // 针对读完的数组$result 取前8列，排除$number行
            $newResult = \frontend\extend\PHPExcel\Excel::getInstance()->handleSheetArray($result, 3, 1);

            //批量入库操作
            if (!empty($newResult)) {
                $count = count($newResult); //统计总记录数
                $f_total = array();
                if($count > 500){
                    $res = array('status'=>4, 'msg'=>'每次最多导入不超过500条记录');
                    die(json_encode($res));
                }
                foreach ($newResult as $key=>$value) {
                    $key = $key+2;//excel默认从1开始加上标题1
                    if (empty($value[0]) || empty($value[1]) || empty($value[2])) {
                        $f_total[] = $key;
                        continue;
                    }
                    $delivery = (new \yii\db\Query())->select('delivery_id')->from(Yii::$app->getDb()->tablePrefix.'expressway')->where(['name'=>trim($value[1])])->one(); //物流编号
                    if(empty($delivery)){
                        $f_total[] = $key;
                        continue;
                    }
                    //判断数据是否已经出库
                    $order_count = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'order')->where(['order_no'=>trim($value[0]),'delivery_status'=>1])->count();
                    if($order_count>0){
                        $f_total[] = $key;
                        continue;
                    }

                    //按订单编号更新：物流公司、物流单号
                    $row = yii::$app->db->createCommand()
                        ->update(Yii::$app->getDb()->tablePrefix.'order', [
                            'delivery_id'    => $delivery['delivery_id'],
                            'delivery_name' => trim($value[1]),
                            'delivery_code' => trim($value[2]),
                            'delivery_status'   => 1, //出库状态，1、出库
                            'confirm_time'  => time(), //审核时间
                            'confirm_user_id'   => Yii::$app->user->id, //审核人id
                            'confirm_user_name' => Yii::$app->user->identity->real_name //审核人名称
                        ],'order_no='.trim($value[0]))->execute();
                    if(!$row){
                        $f_total[] = $key;
                    }
                }
                $f_count = count($f_total);
                $arr = array(
                    'f_line'    => implode(',', $f_total), //失败条目的在模板中的行号,
                    'f_total'   => $f_count, //失败条目数
                    't_total'   => $count-$f_count //成功条目数
                );
            }
            $res = array('status'=>0, 'msg'=>$arr);
            die(json_encode($res));

        }
    }

    public function actionView($id)
    {
        $query = new \yii\db\Query();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        //商品及赠品
        $goodlist = $query->select('g.name,g.spec,b.name as bname,og.sell_price,og.number,og.unit_name,og.batch_num')->from($tablePrefix.'order_goods as og')
            ->leftJoin($tablePrefix.'goods as g','og.goods_id=g.goods_id')
            ->leftJoin($tablePrefix.'brand as b','g.brand_id=b.brand_id')
            ->where('og.order_id='.$id)->orderBy(['og.id'=>SORT_ASC])->all();

        return $this->render('view', [
            'model' => OrderModel::findOne(['order_id'=>$id]),
            'goodlist'  => $goodlist,
        ]);
    }
}