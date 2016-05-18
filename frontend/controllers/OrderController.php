<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Order;
use frontend\models\OrderSearch;
use frontend\models\OrderGoods;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends CommonController
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = new \yii\db\Query();
        $searchModel = new OrderSearch();
        $s_con = Yii::$app->request->queryParams;

        //搜索条件
        $sale_time_start = strtotime(Yii::$app->request->get('sale_time_start')); //销售开始时间
        $sale_time_end = strtotime(Yii::$app->request->get('sale_time_end')); //销售结束时间
        $where='';
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['store_id'] = $store_id;
        }

        if (!empty($s_con['warehouse_id'])) $where['warehouse_id'] = $s_con['warehouse_id'];//仓库
        if (!empty($s_con['shop_id'])) $where['shop_id'] = $s_con['shop_id'];//销售平台
        if (isset($s_con['purchases_status'])) $where['delivery_status'] = $s_con['delivery_status']; //出库状态

        $order = $query->from($tablePrefix.'order')
            ->select('order_id,warehouse_id,warehouse_name,shop_id,shop_name,order_no,real_pay,sale_time,customer_id,customer_name,delivery_status')
            ->where($where)->orderBy(['order_id'=>SORT_DESC]);
        if (!empty($s_con['order_no'])) $query->andFilterWhere(['like', 'order_no',$s_con['order_no']]); //订单编号
        if (!empty($s_con['customer_name'])) $query->andFilterWhere(['like', 'customer_name',$s_con['customer_name']]); //客户帐号
        if($sale_time_start<=$sale_time_end) {
            if(!empty($sale_time_start)) $query->andWhere(['>=','sale_time',$sale_time_start]);
            if(!empty($sale_time_end)) $query->andWhere(['<=','sale_time',$sale_time_end]);
        }
        $countQuery = clone $order;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>15]);
        $dataProvider = $order->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $res = array();
        if($dataProvider){
            foreach($dataProvider as $k=>$v){
                $res[$k]['order_id'] = $v['order_id'];
                $res[$k]['warehouse_id'] = $v['warehouse_id'];
                $res[$k]['warehouse_name'] = $v['warehouse_name'];
                $res[$k]['shop_id'] = $v['shop_id'];
                $res[$k]['shop_name'] = $v['shop_name'];
                $res[$k]['order_no'] = $v['order_no'];
                $res[$k]['real_pay'] = $v['real_pay'];
                $res[$k]['sale_time'] = $v['sale_time'];
                $res[$k]['customer_id'] = $v['customer_id'];
                $res[$k]['customer_name'] = $v['customer_name'];
                $res[$k]['delivery_status'] = $v['delivery_status'];

                $res[$k]['data'] = $query->select('goods_name,spec,number')->from($tablePrefix.'order_goods')->where(['order_id'=>$v['order_id']])->all();
            }
        }

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['仓库','销售平台','订单编号', '商品中英文名称（含规格）','商品数量','实收款','销售日期','客户帐号','出库状态']];

            foreach ($res as $row) {
                $name_spec = array();
                $number = array();
                foreach($row['data'] as $value){
                    $name_spec[] =  $value['goods_name'].' '.$value['spec'];
                    $number[] = $value['number'];
                }

                $final[] = [
                    $row['warehouse_name'],$row['shop_name'],$row['order_no']."\t",implode('|', $name_spec),implode('|', $number),$row['real_pay'],date('Y-m-d',$row['sale_time']),$row['customer_name'],($row['delivery_status']==0)?"否":"是",
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }

        //ajax 生成身份证上传网址
        if(Yii::$app->request->post('action')=='cardUrl'){

            $accept_mobile = Yii::$app->request->post('accept_mobile');//收货人电话
            $accept_name = Yii::$app->request->post('accept_name');//收货人姓名
            $order_no = Yii::$app->request->post('order_no');//订单编号
            $shop_name = Yii::$app->request->post('shop_name');//销售平台

            $hf = 'http://idcard.vitagou.com/?accept_mobile='.base64_encode($accept_mobile).'&accept_name='.base64_encode($accept_name).'&order_no='.base64_encode($order_no).'&shop_name='.base64_encode($shop_name);
            die(json_encode($hf));
        }

        //ajax 根据客户帐号调整收货信息
        if(Yii::$app->request->post('action')=='address_info'){

            $customers_id = Yii::$app->request->post('customers_id');//客户id
            if($customers_id>0){
                $map['customers_id'] = $customers_id;
            }else{
                $map = '';
            }

            $address_data = $query->select('*')->from($tablePrefix.'address')->where($map)->orderBy(['create_time'=>SORT_DESC])->all();
            die(json_encode($address_data));
        }

        //ajax 根据仓库筛选商品信息
        if(Yii::$app->request->get('action')=='goods_info'){
            $wh = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $wh['store_id'] = $store_id;
            }
            $warehouse_id = Yii::$app->request->get('warehouse_id');
            if(!empty($warehouse_id)){
                $wh['warehouse_id'] = $warehouse_id;
            }

            $goods_data = (new \yii\db\Query())->from($tablePrefix.'stocks')
                ->select('*')
                ->where($wh)->orderBy(['purchase_time'=>SORT_DESC])->all();


            foreach($goods_data as $k=>$v){
                $data[]= array(
                    'title'=>$v['goods_name'],
                    'result'=>array(
                        'brand_name'=>$v['brand_name'],
                        'spec'=>$v['spec'],
                        'unit_name'=>$v['unit_name'],
                        'barode_code'=>$v['barode_code'],
                        'goods_id'=>$v['goods_id'],
                        'brand_id'=>$v['brand_id'],
                        'brand_name'=>$v['brand_name'],
                        'unit_id'=>$v['unit_id']
                    )
                );
            }
            die(json_encode(array('data'=>$data)));
        }

        //ajax 根据仓库与商品id筛选采购批号
        if(Yii::$app->request->get('action')=='f_batch'){
            $wh = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $wh['store_id'] = $store_id;
            }
            $warehouse_id = Yii::$app->request->get('warehouse_id');
            if(!empty($warehouse_id)){
                $wh['warehouse_id'] = $warehouse_id;
            }
            $goods_id = Yii::$app->request->get('goods_id');
            if(!empty($goods_id) && $goods_id!='undefined'){
                $wh['goods_id'] = $goods_id;
            }

            $goods_data = (new \yii\db\Query())->from($tablePrefix.'stocks')
                ->select('*')
                ->where($wh)->orderBy(['purchase_time'=>SORT_DESC])->all();


            foreach($goods_data as $k=>$v){
                $data[]= array(
                    'title'=>$v['batch_num'].'(库存'.$v['stock_num'].$v['unit_name'].')',
                    'result'=>array(
                        'batch_num'=>$v['batch_num'],
                        'stocks_id'=>$v['stocks_id']
                    )
                );
            }
            die(json_encode(array('data'=>$data)));
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $res,
            'pages' => $pages,
        ]);
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionCount()
    {
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = new \yii\db\Query();
        $searchModel = new OrderSearch();
        $s_con = Yii::$app->request->queryParams;
        //搜索条件
        $create_time_start = strtotime(Yii::$app->request->get('create_time_start')); //创建开始时间
        $create_time_end = strtotime(Yii::$app->request->get('create_time_end')); //创建结束时间
        $where['delivery_status']=0;
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['store_id'] = $store_id;
        }
        if (!empty($s_con['warehouse_name'])) $where['warehouse_name']= $s_con['warehouse_name']; //仓库
        if (!empty($s_con['address_id'])) $where['address_id'] = $s_con['address_id'];//收货人

        $order = $query->from($tablePrefix.'order')
            ->select('order_id,warehouse_id,warehouse_name,shop_id,address_id,shop_name,order_no,real_pay,create_time')
            ->where($where);
        if (!empty($s_con['order_no'])) $query->andFilterWhere(['like', 'order_no',$s_con['order_no']]); //订单编号
        if($create_time_start<=$create_time_end) {
            if(!empty($create_time_start)) $query->andWhere(['>=','create_time',$create_time_start]);
            if(!empty($create_time_end))  $query->andWhere(['<=','create_time',$create_time_end]);
        }
        $countQuery = clone $order;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>15]);
        $dataProvider = $order->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $res = array();
        if($dataProvider){
            foreach($dataProvider as $k=>$v){
                $res[$k]['order_id'] = $v['order_id'];
                $res[$k]['warehouse_id'] = $v['warehouse_id'];
                $res[$k]['warehouse_name'] = $v['warehouse_name'];
                $res[$k]['shop_id'] = $v['shop_id'];
                $res[$k]['shop_name'] = $v['shop_name'];
                $res[$k]['order_no'] = $v['order_no'];
                $res[$k]['real_pay'] = $v['real_pay'];
                $res[$k]['create_time'] = $v['create_time'];

                $res[$k]['address'] = $query->select('accept_name,accept_mobile,accept_address,accept_idcard,zcode')->from($tablePrefix.'address')->where(['address_id'=>$v['address_id']])->one();
                $res[$k]['data'] = $query->select('goods_id,goods_name,spec,brand_name,number')->from($tablePrefix.'order_goods')->where(['order_id'=>$v['order_id']])->all();
            }
        }

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['发货仓库','收货人','联系电话', '收货地址','邮政编码','证件号码','商品中英文名称（含规格）','品牌','条形码','商品数量','实收款','创建时间']];

            foreach ($res as $row) {
                $name_spec = array();
                $brand_name = array();
                $number = array();
                $barode_code = array();
                foreach($row['data'] as $value){
                    $name_spec[] =  $value['goods_name'].'&nbsp'.$value['spec'];
                    $brand_name[] = $value['brand_name'];
                    $number[] = $value['number'];
                    $goods = $query->select('barode_code')->from($tablePrefix.'goods')->where('goods_id='.$value['goods_id'])->one();
                    $barode_code[] = $goods['barode_code'];
                }

                $final[] = [
                    $row['warehouse_name'],$row['address']['accept_name'],$row['address']['accept_mobile'],$row['address']['accept_address'],$row['address']['zcode'],$row['address']['accept_idcard']."\t",implode('|', $name_spec),implode('|', $brand_name),implode('|', $barode_code),implode('|', $number),$row['real_pay'],date('Y-m-d H:i:s',$row['create_time']),
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }

        return $this->render('count', [
            'searchModel' => $searchModel,
            'dataProvider' => $res,
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
        $goodlist = $query->select('g.name,g.spec,b.name as bname,og.sell_price,og.number')->from($tablePrefix.'order_goods as og')
            ->leftJoin($tablePrefix.'goods as g','og.goods_id=g.goods_id')
            ->leftJoin($tablePrefix.'brand as b','g.brand_id=b.brand_id')
            ->where('og.order_id='.$id)->all();

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
        $model = new Order();
        $og_model = new OrderGoods();

        if ($model->load(Yii::$app->request->post())) {

            $order = Yii::$app->request->post('Order');

            $model->order_no = $order['order_no'];
            $model->store_id = $order['store_id'];
            $model->store_name = $order['store_name'];
            $model->warehouse_id = $order['warehouse_id'];
            $model->warehouse_name = $order['warehouse_name'];
            $model->shop_id = $order['shop_id'];
            $model->shop_name = $order['shop_name'];
            $model->real_pay = $order['real_pay'];
            $model->discount = $order['discount'];
            $model->address_id = $order['address_id'];
            $model->remark = $order['remark'];
            $model->create_time = $order['create_time'];
            $model->add_user_id = $order['add_user_id'];
            $model->add_user_name = $order['add_user_name'];
            $model->customer_id = $order['customer_id'];
            $model->customer_name = $order['customer_name'];
            $model->sale_time = strtotime($order['sale_time']);

            if($model->save()){

                $order_goods = Yii::$app->request->post('OrderGoods');
                $count = count($order_goods['goods_id']);

                for($i=0; $i<$count; $i++){

                    yii::$app->db->createCommand()
                        ->insert(Yii::$app->getDb()->tablePrefix.'order_goods', [
                            'order_id' => $model->attributes['order_id'],
                            'goods_id' => $order_goods['goods_id'][$i],
                            'goods_name' => $order_goods['goods_name'][$i],
                            'brand_id' => $order_goods['brand_id'][$i],
                            'brand_name' => $order_goods['brand_name'][$i],
                            'spec' => $order_goods['spec'][$i],
                            'batch_num' => $order_goods['batch_num'][$i],
                            'stocks_id' => !empty($order_goods['stocks_id'][$i]) ? $order_goods['stocks_id'][$i] : 0,
                            'sell_price' => $order_goods['sell_price'][$i],
                            'number' => $order_goods['number'][$i],
                            'unit_id' => $order_goods['unit_id'][$i],
                            'unit_name' => $order_goods['unit_name'][$i]
                        ])->execute();
                }
                return $this->redirect(['index']);
            }


        } else {
            return $this->render('create', [
                'model' => $model,
                'og_model'  => $og_model,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $og_model = $this->findOgModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $order = Yii::$app->request->post('Order');

            $model->order_no = $order['order_no'];
            $model->warehouse_id = $order['warehouse_id'];
            $model->warehouse_name = $order['warehouse_name'];
            $model->shop_id = $order['shop_id'];
            $model->shop_name = $order['shop_name'];
            $model->real_pay = $order['real_pay'];
            $model->discount = $order['discount'];
            $model->address_id = $order['address_id'];
            $model->remark = $order['remark'];
            $model->customer_id = $order['customer_id'];
            $model->customer_name = $order['customer_name'];
            $model->sale_time = strtotime($order['sale_time']);

            if($model->save()){
                $order_goods = Yii::$app->request->post('OrderGoods');

                $count = count($order_goods['goods_id']);
                yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'order_goods','order_id='.$id)->execute();
                for($i=0; $i<$count; $i++){

                    yii::$app->db->createCommand()
                        ->insert(Yii::$app->getDb()->tablePrefix.'order_goods', [
                            'order_id' => $model->attributes['order_id'],
                            'goods_id' => $order_goods['goods_id'][$i],
                            'goods_name' => $order_goods['goods_name'][$i],
                            'brand_id' => $order_goods['brand_id'][$i],
                            'brand_name' => $order_goods['brand_name'][$i],
                            'spec' => $order_goods['spec'][$i],
                            'batch_num' => $order_goods['batch_num'][$i],
                            'stocks_id' => !empty($order_goods['stocks_id'][$i]) ? $order_goods['stocks_id'][$i] : 0,
                            'sell_price' => $order_goods['sell_price'][$i],
                            'number' => $order_goods['number'][$i],
                            'unit_id' => $order_goods['unit_id'][$i],
                            'unit_name' => $order_goods['unit_name'][$i]
                        ])->execute();
                }
            }

            return $this->redirect(['index']);

        } else {
            return $this->render('update', [
                'model' => $model,
                'og_model'  => $og_model,
            ]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'order_goods','order_id='.$id)->execute();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findOgModel($id)
    {
        if (($og_model = OrderGoods::findAll(['order_id'=>$id])) !== null) {
            return $og_model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
