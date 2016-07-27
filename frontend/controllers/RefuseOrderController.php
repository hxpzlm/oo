<?php

namespace frontend\controllers;

use Yii;
use frontend\models\RefuseOrder;
use frontend\models\RefuseOrderSearch;
use frontend\models\RefuseOrderGoods;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * RefuseOrderController implements the CRUD actions for RefuseOrder model.
 */
class RefuseOrderController extends CommonController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],

        ];
    }

    /**
     * Lists all RefuseOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = new \yii\db\Query();
        $searchModel = new RefuseOrderSearch();
        $s_con = Yii::$app->request->queryParams;

        //搜索条件
        $refuse_time_start = strtotime(Yii::$app->request->get('refuse_time_start')); //退货开始时间
        $refuse_time_end = strtotime(Yii::$app->request->get('refuse_time_end').' 23:59:59'); //退货结束时间
        $where='';
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['ro.store_id'] = $store_id;
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['ro.store_id'] = $sid;
            }
        }

        if (!empty($s_con['warehouse_id'])) $where['ro.warehouse_id'] = $s_con['warehouse_id']; //仓库
        if (!empty($s_con['shop_id'])) $where['ro.shop_id'] = $s_con['shop_id'];//销售平台

        if(isset($s_con['status'])){//入库状态
            if($s_con['status']==1)  $where['ro.status'] = $s_con['status'];
            if($s_con['status']==2)  $where['ro.status'] = 0;
        }

        $refuse_order = $query->from($tablePrefix.'refuse_order as ro')
        ->select('ro.refuse_id,ro.shop_id,ro.shop_name,ro.order_no,ro.refuse_amount,ro.refuse_time,ro.warehouse_id,ro.warehouse_name,ro.status,ro.confirm_time,ro.create_time')
            ->innerJoin(['rog'=>$tablePrefix.'refuse_order_goods'],'ro.refuse_id=rog.refuse_id')
            ->where($where)->orderBy(['ro.refuse_id'=>SORT_DESC])->groupBy(['ro.refuse_id']);
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'rog.goods_name',$s_con['goods_name']]); //商品中英文名
        if (!empty($s_con['customer_name'])) $query->andFilterWhere(['like', 'ro.customer_name',$s_con['customer_name']]); //客户帐号
        if($refuse_time_start<=$refuse_time_end) {
            if(!empty($refuse_time_start)) $query->andWhere(['>=','ro.refuse_time',$refuse_time_start]);
            if(!empty($refuse_time_end))  $query->andWhere(['<=','ro.refuse_time',$refuse_time_end]);
        }
        $countQuery = clone $refuse_order;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $refuse_order->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['销售平台','订单编号','退货商品中英文名称（含规格）', '退货数量','退货金额','退货日期','入库仓库','入库状态','入库日期']];

            foreach ($countQuery->all() as $row) {
                $name_spec = array();
                $number = array();
                foreach((new \yii\db\Query())->select('goods_name,spec,number')->from($tablePrefix.'refuse_order_goods')->where(['refuse_id'=>$row['refuse_id']])->all() as $value){
                    $name_spec[] =  $value['goods_name'].' '.$value['spec'];
                    $number[] = $value['number'];
                }

                $final[] = [
                    $row['shop_name'],$row['order_no']."\t",$name_spec,$number,$row['refuse_amount'],date('Y-m-d',$row['refuse_time']),$row['warehouse_name'],($row['status']==0)?"否":"是",date('Y-m-d',$row['confirm_time'])
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }

        //ajax获取商品及赠品
        if(Yii::$app->request->post('action')=='goods'){

            $order_id = Yii::$app->request->post('order_id');
            $rog_model = (new \yii\db\Query())->select('og.id,og.goods_id,og.brand_id,og.unit_id,og.goods_name,og.spec,og.brand_name,og.batch_num,og.sell_price,og.number,og.unit_name,og.stocks_id,s.stock_num')
                ->from(Yii::$app->getDb()->tablePrefix.'order_goods as og')
                ->leftJoin(Yii::$app->getDb()->tablePrefix.'stocks as s','og.stocks_id=s.stocks_id')
                ->where('og.order_id='.$order_id)->all();

            die(json_encode($rog_model));
        }

        //ajax订单编号是否存在
        if(Yii::$app->request->post('action')=='oorder_no'){
            $order_no = Yii::$app->request->post('order_no');
            $gw = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $gw['store_id'] = $store_id;
            }
            if(!empty($order_no)){
                $gw['order_no'] = $order_no;
            }
            $order_count = (new \yii\db\Query())->from($tablePrefix.'order')->where($gw)->count();
            die(json_encode($order_count));
        }

        //ajax订单编号是否存在
        if(Yii::$app->request->get('action')=='f_order_no'){
            $shop_id = Yii::$app->request->get('shop_id');
            $sw['delivery_status'] = 1;
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $sw['store_id'] = $store_id;
            }
            if(!empty($shop_id)){
                $sw['shop_id'] = $shop_id;
            }
            $order_no = Yii::$app->request->get('order_no');
            $orders = (new \yii\db\Query())->select('*')->from($tablePrefix.'order')->where($sw);
            if (!empty($order_no)) $orders->andFilterWhere(['like', 'order_no',$order_no]); //订单编号
            foreach($orders->all() as $v){
                if(!empty($v['order_no'])){
                    $real_name = (new \yii\db\Query())->select('username,real_name')->from($tablePrefix.'customers')->where('customers_id='.$v['customer_id'])->one();//客服真实姓名
                    $data[]= array('title'=>$v['order_no'],'result'=>array('order_id'=>$v['order_id'],'real_pay'=>$v['real_pay'],'sale_time'=>date('Y-m-d', $v['sale_time']),'customer_id'=>$v['customer_id'],'customer_name'=>!empty($real_name['username'])?$real_name['username']:'','real_name'=>!empty($real_name['real_name'])?$real_name['real_name']:''));
                }
            }
            die(json_encode(array('data'=>$data)));
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Displays a single Order model.
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

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RefuseOrder();
        $rog_model = new RefuseOrderGoods();

        if ($model->load(Yii::$app->request->post())) {

            $refuse_order = Yii::$app->request->post('RefuseOrder');

            //验证仓库是否存在商品
            if(!empty($refuse_order['shop_id'])){
                $shop_id = $refuse_order['shop_id'];
                $sw['delivery_status'] = 1;
                $store_id = Yii::$app->user->identity->store_id;
                if($store_id>0){
                    $sw['store_id'] = $store_id;
                }
                if(!empty($shop_id)){
                    $sw['shop_id'] = $shop_id;
                }
                $orders = (new \yii\db\Query())->select('*')->from(Yii::$app->getDb()->tablePrefix.'order')->where($sw)->orderBy('')->all();
                if(empty($orders)){
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                    echo "<script>alert('所选销售平台下不存在此订单编号');location.href='';</script>";
                    exit;
                }
            }

            $model->store_id = $refuse_order['store_id'];
            $model->store_name = $refuse_order['store_name'];
            $model->create_time = $refuse_order['create_time'];
            $model->add_user_id = $refuse_order['add_user_id'];
            $model->add_user_name = $refuse_order['add_user_name'];
            $model->shop_id = $refuse_order['shop_id'];
            $model->shop_name = $refuse_order['shop_name'];
            $model->order_no = $refuse_order['order_no'];
            $model->order_id = $refuse_order['order_id'];
            $model->refuse_real_pay = $refuse_order['refuse_real_pay'];
            $model->sale_time = strtotime($refuse_order['sale_time']);
            $model->customer_id = $refuse_order['customer_id'];
            $model->customer_name = $refuse_order['customer_name'];
            $model->refuse_amount = $refuse_order['refuse_amount'];
            $model->refuse_time = strtotime($refuse_order['refuse_time']);
            $model->reason = $refuse_order['reason'];
            $model->warehouse_id = $refuse_order['warehouse_id'];
            $model->warehouse_name = $refuse_order['warehouse_name'];
            $model->remark = $refuse_order['remark'];

            if($model->save()){

                $refuse_order_goods = Yii::$app->request->post('RefuseOrderGoods');
                for($i=0; $i<count($refuse_order_goods['goods_name']); $i++){

                    yii::$app->db->createCommand()
                        ->insert(Yii::$app->getDb()->tablePrefix.'refuse_order_goods', [
                            'refuse_id' => $model->attributes['refuse_id'],
                            'goods_id' => $refuse_order_goods['goods_id'][$i],
                            'goods_name' => $refuse_order_goods['goods_name'][$i],
                            'brand_id' => $refuse_order_goods['brand_id'][$i],
                            'stocks_id' => $refuse_order_goods['stocks_id'][$i],
                            'brand_name' => $refuse_order_goods['brand_name'][$i],
                            'spec' => $refuse_order_goods['spec'][$i],
                            'batch_num' => $refuse_order_goods['batch_num'][$i],
                            'sell_price' => $refuse_order_goods['sell_price'][$i],
                            'number' => $refuse_order_goods['number'][$i],
                            'unit_id' => $refuse_order_goods['unit_id'][$i],
                            'unit_name' => $refuse_order_goods['unit_name'][$i]
                        ])->execute();
                }
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RefuseOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $rog_model = $this->findRogModel($id);
        $tablePrefix = Yii::$app->getDb()->tablePrefix;

        if ($model->load(Yii::$app->request->post())) {

            $refuse_order = Yii::$app->request->post('RefuseOrder');

            //验证仓库是否存在商品
            if(!empty($refuse_order['shop_id'])){
                $shop_id = $refuse_order['shop_id'];
                $sw['delivery_status'] = 1;
                $store_id = Yii::$app->user->identity->store_id;
                if($store_id>0){
                    $sw['store_id'] = $store_id;
                }
                if(!empty($shop_id)){
                    $sw['shop_id'] = $shop_id;
                }
                $orders = (new \yii\db\Query())->select('*')->from($tablePrefix.'order')->where($sw)->orderBy('')->all();
                if(empty($orders)){
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                    echo "<script>alert('所选销售平台下不存在此订单编号');location.href='';</script>";
                    exit;
                }
            }

            $model->shop_id = $refuse_order['shop_id'];
            $model->shop_name = $refuse_order['shop_name'];
            $model->order_no = $refuse_order['order_no'];
            $model->order_id = $refuse_order['order_id'];
            $model->refuse_real_pay = $refuse_order['refuse_real_pay'];
            $model->sale_time = strtotime($refuse_order['sale_time']);
            $model->customer_id = $refuse_order['customer_id'];
            $model->customer_name = $refuse_order['customer_name'];
            $model->refuse_amount = $refuse_order['refuse_amount'];
            $model->refuse_time = strtotime($refuse_order['refuse_time']);
            $model->reason = $refuse_order['reason'];
            $model->warehouse_id = $refuse_order['warehouse_id'];
            $model->warehouse_name = $refuse_order['warehouse_name'];
            $model->remark = $refuse_order['remark'];

            if($model->save()){
                $refuse_order_goods = Yii::$app->request->post('RefuseOrderGoods');

                yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'refuse_order_goods','refuse_id='.$id)->execute();
                $count = count($refuse_order_goods['goods_id']);
                for($i=0; $i<$count; $i++){
                    yii::$app->db->createCommand()
                        ->insert($tablePrefix.'refuse_order_goods', [
                            'id'    => $refuse_order_goods['id'][$i],
                            'refuse_id' => $model->attributes['refuse_id'],
                            'goods_id' => $refuse_order_goods['goods_id'][$i],
                            'goods_name' => $refuse_order_goods['goods_name'][$i],
                            'brand_id' => $refuse_order_goods['brand_id'][$i],
                            'stocks_id' => $refuse_order_goods['stocks_id'][$i],
                            'brand_name' => $refuse_order_goods['brand_name'][$i],
                            'spec' => $refuse_order_goods['spec'][$i],
                            'batch_num' => $refuse_order_goods['batch_num'][$i],
                            'sell_price' => $refuse_order_goods['sell_price'][$i],
                            'number' => $refuse_order_goods['number'][$i],
                            'unit_id' => $refuse_order_goods['unit_id'][$i],
                            'unit_name' => $refuse_order_goods['unit_name'][$i]
                        ])->execute();

                }
            }

            return $this->redirect(['index']);


        } else {
            return $this->render('update', [
                'model' => $model,
                'rog_model'  => $rog_model,
            ]);
        }
    }

    /**
     * Deletes an existing RefuseOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'refuse_order_goods','refuse_id='.$id)->execute();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RefuseOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RefuseOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RefuseOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the RefuseOrderGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RefuseOrderGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRogModel($id)
    {
        if($rog_model = RefuseOrderGoods::findAll(['refuse_id'=>$id])){
            return $rog_model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
