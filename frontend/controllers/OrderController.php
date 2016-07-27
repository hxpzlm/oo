<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Order;
use frontend\models\OrderSearch;
use frontend\models\OrderGoods;
use frontend\models\Customers;
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
        $sale_time_end = strtotime(Yii::$app->request->get('sale_time_end').' 23:59:59'); //销售结束时间
        $where='';
        if(isset($s_con['delivery_status'])){//出库状态
            if($s_con['delivery_status']==1)  $where['o.delivery_status'] = $s_con['delivery_status'];
            if($s_con['delivery_status']==2)  $where['o.delivery_status'] = 0;
        }
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['o.store_id'] = $store_id;
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['o.store_id'] = $sid;
            }
        }

        if (!empty($s_con['warehouse_id'])) $where['o.warehouse_id'] = $s_con['warehouse_id'];//仓库
        if (!empty($s_con['shop_id'])) $where['o.shop_id'] = $s_con['shop_id'];//销售平台

        $order = $query->from($tablePrefix.'order as o')
            ->select('o.order_id,o.warehouse_id,o.warehouse_name,o.shop_id,o.shop_name,o.order_no,o.real_pay,o.sale_time,o.customer_id,o.customer_name,o.delivery_status,o.delivery_code')
            ->innerJoin(['og'=>$tablePrefix.'order_goods'],'o.order_id=og.order_id')
            ->leftJoin(['a'=>$tablePrefix.'address'],'o.address_id=a.address_id')
            ->where($where)->orderBy(['o.order_id'=>SORT_DESC])->groupBy(['o.order_id']);
        if (!empty($s_con['order_no'])) $order->andFilterWhere(['like', 'o.order_no',$s_con['order_no']]); //订单编号
        if (!empty($s_con['goods_name'])) $order->andFilterWhere(['like', 'og.goods_name',$s_con['goods_name']]); //商品名称
        if (!empty($s_con['delivery_code'])) $order->andFilterWhere(['like', 'o.delivery_code',$s_con['delivery_code']]); //物流单号
        if (!empty($s_con['customer_name'])) $order->andFilterWhere(['like', 'o.customer_name',$s_con['customer_name']]); //客户帐号
        if (!empty($s_con['accept_name'])) $order->andFilterWhere(['like', 'a.accept_name',$s_con['accept_name']]); //收货人
        if($sale_time_start<=$sale_time_end) {
            if(!empty($sale_time_start)) $order->andWhere(['>=','o.sale_time',$sale_time_start]);
            if(!empty($sale_time_end)) $order->andWhere(['<=','o.sale_time',$sale_time_end]);
        }
        $countQuery = clone $order;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $order->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['仓库','销售平台','订单编号', '商品中英文名称（含规格）','商品数量','实收款','销售日期','客户帐号','出库状态','物流单号']];
            foreach ($countQuery->all() as $row) {
                $name_spec = array();
                $number = array();
                foreach((new \yii\db\Query())->select('goods_name,spec,number')->from($tablePrefix.'order_goods')->where(['order_id'=>$row['order_id']])->orderBy(['id'=>SORT_ASC])->all() as $value){
                    $name_spec[] =  $value['goods_name'].' '.$value['spec'];
                    $number[] = $value['number'];
                }
                $final[] = [
                    $row['warehouse_name'],$row['shop_name'],$row['order_no']."\t",$name_spec,$number,$row['real_pay'],date('Y-m-d',$row['sale_time']),$row['customer_name'],($row['delivery_status']==0)?"否":"是",$row['delivery_code']."\t",
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
            $msg = '';
            $mobile = Yii::$app->request->post('accept_mobile');//收货人电话
            $name = Yii::$app->request->post('accept_name');//收货人姓名
            $orderid = Yii::$app->request->post('order_no');//订单编号
            $from = Yii::$app->request->post('shop_name');//销售平台

            $msg = 'http://id.vitagou.com?'.base64_encode('mobile='.$mobile.'&name='.$name.'&orderid='.$orderid.'&from='.$from);
            die(json_encode($msg));
        }

        //ajax 根据客户帐号调整收货信息
        if(Yii::$app->request->post('action')=='address_info'){

            $customers_id = Yii::$app->request->post('customers_id');//客户id
            if($customers_id>0){
                $map['customers_id'] = $customers_id;
            }else{
                $map = '';
            }

            $address_data = (new \yii\db\Query())->select('*')->from($tablePrefix.'address')->where($map)->orderBy(['create_time'=>SORT_DESC])->all();
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
                //查询采购入库的商品以及库存数
                $goods_data = (new \yii\db\Query())->from($tablePrefix.'stocks')
                    ->select('*')
                    ->where($wh)->orderBy(['purchase_time'=>SORT_DESC]);
                $goods_data->andWhere(['>','stock_num',0]);
                $goods_data->andWhere(['!=','batch_num','']);
                $goods_name = Yii::$app->request->get('goods_name');
                if (!empty($goods_name)) $goods_data->andFilterWhere(['like', 'goods_name',$goods_name]); //订单编号
                $goods_list = $goods_data->groupBy(['goods_id'])->all();
                die(json_encode($goods_list));
            }

        }

        //ajax 根据仓库与商品id筛选采购批号
        if(Yii::$app->request->get('action')=='f_batch'){
            $wh = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $wh['s.store_id'] = $store_id;
            }
            $warehouse_id = Yii::$app->request->get('warehouse_id');
            if(!empty($warehouse_id)){
                $wh['s.warehouse_id'] = $warehouse_id;
            }
            $goods_id = Yii::$app->request->get('goods_id');
            if(!empty($goods_id)){
                $wh['s.goods_id'] = $goods_id;

                $goods_data = (new \yii\db\Query())->from($tablePrefix.'stocks as s')
                    ->select('s.*')
                    ->leftJoin(['p' => $tablePrefix.'purchase'],'s.purchase_id = p.purchase_id')
                    ->where($wh)->orderBy(['p.invalid_time'=>SORT_ASC]);
                $goods_data->andWhere(['>','s.stock_num',0]);
                $goods_data->andWhere(['!=','s.batch_num','']);
                $goods_list = $goods_data->all();;
                die(json_encode($goods_list));
            }

        }

        //ajax客户帐号是否存在
        if(Yii::$app->request->post('action')=='c_exist'){
            $customer_name = Yii::$app->request->post('customer_name');
            $cw = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $cw['store_id'] = $store_id;
            }
            if(!empty($customer_name)){
                $cw['username'] = $customer_name;
            }
            $customer_info = (new \yii\db\Query())->select('*')->from($tablePrefix.'customers')->where($cw)->one();
            die(json_encode($customer_info));
        }

        //ajax判断商品中英文名称是否存在
        if(Yii::$app->request->post('action')=='gname'){
            $goods_name = Yii::$app->request->post('goods_name');
            $gw = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $gw['store_id'] = $store_id;
            }
            if(!empty($goods_name)){
                $gw['name'] = $goods_name;
            }
            $goods_info = (new \yii\db\Query())->select('*')->from($tablePrefix.'goods')->where($gw)->count();

            die(json_encode($goods_info));
        }

        //ajax判断采购批号是否存在
        if(Yii::$app->request->post('action')=='b_batchnum'){
            $bh = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $bh['store_id'] = $store_id;
            }
            $warehouse_id = Yii::$app->request->post('warehouse_id');
            if(!empty($warehouse_id)){
                $bh['warehouse_id'] = $warehouse_id;
            }
            $goods_id = Yii::$app->request->post('goods_id');
            if(!empty($goods_id)){
                $bh['goods_id'] = $goods_id;
            }
            $batch_num = Yii::$app->request->post('batch_num');
            $batch_arr = explode('(',$batch_num);
            if(!empty($batch_arr[0])){
                $bh['batch_num'] = $batch_arr[0];
            }
            $batch_count = (new \yii\db\Query())->from($tablePrefix.'stocks')
                ->select('*')
                ->where($bh)->count();

            die(json_encode($batch_count));
        }

        //ajax 新建客户
        if(Yii::$app->request->post('action')=='ajaxOca'){
            $c_model = new Customers();
            $tablePrefix = Yii::$app->getDb()->tablePrefix;

            $customers = Yii::$app->request->post('Customers');
            $order = Yii::$app->request->post('Order');
            $address = Yii::$app->request->post('Address');

            $c_model->shop_id = $customers['shop_id'];
            $c_model->shop_name = $customers['shop_name'];
            if(empty($c_model->shop_id)){
                $result = array('status'=>1,'msg'=>'请选择客户来源');
                die(json_encode($result));
            }
            $c_model->username = $customers['username'];
            if(empty($c_model->username)){
                $result = array('status'=>2,'msg'=>'客户帐号不能为空');
                die(json_encode($result));
            }
            $c_model->real_name = $customers['real_name'];
            if(empty($c_model->real_name)){
                $result = array('status'=>3,'msg'=>'姓名不能为空');
                die(json_encode($result));
            }
            $c_model->sex = $customers['sex'];
            $c_model->mobile = $customers['mobile'];
            if(empty($c_model->mobile)){
                $result = array('status'=>4,'msg'=>'联系电话不能为空');
                die(json_encode($result));
            }
            $c_model->other = $customers['other'];
            $c_model->address = $customers['address'];
            $c_model->type = $customers['type'];
            $c_model->sort = $customers['sort'];
            $c_model->remark = $customers['remark'];
            $c_model->store_id = $order['store_id'];
            $c_model->store_name = $order['store_name'];
            $c_model->add_user_id = $order['add_user_id'];
            $c_model->add_user_name = $order['add_user_name'];
            $c_model->create_time = $order['create_time'];

            if(empty($address['accept_name'])){
                $result = array('status'=>5,'msg'=>'收货人姓名不能为空');
                die(json_encode($result));
            }
            if(empty($address['accept_mobile'])){
                $result = array('status'=>6,'msg'=>'收货人电话不能为空');
                die(json_encode($result));
            }
            if(empty($address['accept_address'])){
                $result = array('status'=>7,'msg'=>'收货人地址不能为空');
                die(json_encode($result));
            }
            //判断客户帐号是否重复
            if((new \yii\db\Query())->from($tablePrefix.'customers')->where('username='."'.$c_model->username.'")->count()>0){
                $result = array('status'=>8,'msg'=>'客户帐号不能重复');
                die(json_encode($result));
            }
            //判断收货人姓名是否重复
            if((new \yii\db\Query())->from($tablePrefix.'address')->where(['accept_name'=>$address['accept_name'],'customers_id'=>$c_model->attributes['customers_id']])->count()>0){
                $result = array('status'=>9,'msg'=>'收货人姓名不能重复');
                die(json_encode($result));
            }

            if($c_model->save()){
                yii::$app->db->createCommand()
                        ->insert($tablePrefix.'address', [
                            'customers_id' => $c_model->attributes['customers_id'],
                            'accept_name' => $address['accept_name'],
                            'accept_mobile' => $address['accept_mobile'],
                            'accept_address' => $address['accept_address'],
                            'accept_idcard' => $address['accept_idcard'],
                            'is_idcard' => isset($address['is_idcard']) ? $address['is_idcard'] : 0,
                            'zcode' => $address['zcode'],
                            'add_user_id' => $c_model->add_user_id,
                            'add_user_name' => $c_model->add_user_name,
                            'create_time' => $c_model->create_time
                    ])->execute();
            }

            $address_list = (new \yii\db\Query())->select('*')->from($tablePrefix.'address')->where('customers_id='.$c_model->attributes['customers_id'])->all();
            if($address_list){
                $a_html = '<select id="order-address_id" name="Order[address_id]" onchange="$(\'#address-accept_mobile\').text($(\'#order-address_id option:selected\').attr(\'a1\'));$(\'#address-accept_address\').text($(\'#order-address_id option:selected\').attr(\'a2\'));$(\'#address-accept_idcard\').text($(\'#order-address_id option:selected\').attr(\'a3\'));$(\'#address-is_idcard\').text($(\'#order-address_id option:selected\').attr(\'a4\'));$(\'#accept_name\').val($(\'#order-address_id option:selected\').attr(\'a5\'));">';
                $a_html .= '<option a1="" a2="" a3="" a4="" a5="" value="">请选择</option>';
                foreach($address_list as $value){
                    $is_idcard = !empty($value['is_idcard'])?'是':'否';
                    if($value['accept_name']==$address['accept_name']){
                        $a_html .= '<option a1='.$value['accept_mobile'].' a2="'.$value['accept_address'].'" a3="'.$value['accept_idcard'].'" a4="'.$is_idcard.'" a5="'.$value['accept_name'].'" value='.$value['address_id'].' selected="selected">'.$value['accept_name'].'</option>';
                    }else{
                        $a_html .= '<option a1='.$value['accept_mobile'].' a2="'.$value['accept_address'].'" a3="'.$value['accept_idcard'].'" a4="'.$is_idcard.'" a5="'.$value['accept_name'].'" value='.$value['address_id'].'>'.$value['accept_name'].'</option>';
                    }
                }
                $a_html .= '</select>';
            }
            $row = array(
                'customers_id'  => $c_model->attributes['customers_id'],
                'customer_name' => $customers['username'],
                'real_name' => $customers['real_name'],
                'address_id'    => $a_html,
                'accept_name'   => $address['accept_name'],
                'accept_mobile' => $address['accept_mobile'],
                'accept_address'    => $address['accept_address'],
                'accept_idcard' => $address['accept_idcard'],
                'is_idcard' => ($address['is_idcard']==1)?'是':'否'
            );

            $result = array('status'=>10,'msg'=>'添加成功','row'=>$row,'address_id'=>$a_html);
            die(json_encode($result));
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $create_time_end = strtotime(Yii::$app->request->get('create_time_end').' 23:59:59'); //创建结束时间

        $where['o.delivery_status']=0;
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['o.store_id'] = $store_id;
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['o.store_id'] = $sid;
            }
        }
        if (!empty($s_con['warehouse_name'])) $where['o.warehouse_name']= $s_con['warehouse_name']; //仓库

        $order = $query->from($tablePrefix.'order as o')
            ->select('o.order_id,o.warehouse_id,o.warehouse_name,o.shop_id,o.address_id,o.shop_name,o.order_no,o.real_pay,o.delivery_name,o.create_time,a.accept_name,a.accept_mobile,a.accept_address,a.accept_idcard,a.zcode')
            ->leftJoin(['a'=>$tablePrefix.'address'],'o.address_id=a.address_id')
            ->where($where)->orderBy(['o.create_time'=>SORT_DESC])->groupBy(['o.order_id']);

        if (!empty($s_con['order_no'])) $order->andFilterWhere(['like', 'o.order_no',$s_con['order_no']]); //订单编号
        if (!empty($s_con['accept_name'])) $order->andFilterWhere(['like', 'a.accept_name',$s_con['accept_name']]); //收货人
        if($create_time_start<=$create_time_end) {
            if(!empty($create_time_start)) $order->andWhere(['>=','o.create_time',$create_time_start]);
            if(!empty($create_time_end))  $order->andWhere(['<=','o.create_time',$create_time_end]);
        }
        $countQuery = clone $order;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $order->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['发货仓库','收货人','联系电话', '收货地址','邮政编码','证件号码','条形码','品牌','商品中英文名称','规格','数量','单位','实收款','物流公司']];
            foreach ($countQuery->all() as $row) {
                $name = array();
                $spec = array();
                $brand_name = array();
                $number = array();
                $unit = array();
                $barode_code = array();
                foreach((new \yii\db\Query())->select('goods_id,goods_name,spec,brand_name,number')->from($tablePrefix.'order_goods')->where(['order_id'=>$row['order_id']])->orderBy(['id'=>SORT_ASC])->all() as $value){
                    $name[] =  $value['goods_name'];
                    $spec[] = $value['spec'];
                    $brand_name[] = $value['brand_name'];
                    $number[] = $value['number'];
                    $goods = (new \yii\db\Query())->select('barode_code,unit_name')->from($tablePrefix.'goods')->where('goods_id='.$value['goods_id'])->one();
                    $barode_code[] = $goods['barode_code']."\t";
                    $unit[] = $goods['unit_name'];
                }
                $final[] = [
                    $row['warehouse_name'],$row['accept_name'],$row['accept_mobile']."\t",$row['accept_address'],!empty($row['zcode'])?$row['zcode']:'　',$row['accept_idcard']."\t",$barode_code,$brand_name,$name,$spec,$number,$unit,$row['real_pay'],$row['delivery_name'],
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }

        return $this->render('count', [
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
        $goodlist = $query->select('g.name,g.spec,b.name as bname,og.sell_price,og.number,og.unit_name,og.batch_num')->from($tablePrefix.'order_goods as og')
            ->leftJoin($tablePrefix.'goods as g','og.goods_id=g.goods_id')
            ->leftJoin($tablePrefix.'brand as b','g.brand_id=b.brand_id')
            ->where('og.order_id='.$id)->orderBy(['og.id'=>SORT_ASC])->all();

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

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\bootstrap\ActiveForm::validate($model);
            }

            $order = Yii::$app->request->post('Order');
            $order_goods = Yii::$app->request->post('OrderGoods');
            $count = count($order_goods['goods_id']);
            //验证仓库是否存在商品
            if(!empty($order['warehouse_id'])){
                $wh = '';
                $store_id = Yii::$app->user->identity->store_id;
                if($store_id>0){
                    $wh['store_id'] = $store_id;
                }
                $wh['warehouse_id'] = $order['warehouse_id'];
                $goods_name_arr = array();
                for($i=0; $i<$count; $i++){
                    $wh['goods_id'] = $order_goods['goods_id'][$i];
                    $goods_data = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'stocks')
                        ->select('goods_id,goods_name')
                        ->where($wh)->orderBy(['purchase_time'=>SORT_DESC]);
                    (new \yii\db\Query())->andWhere(['>','stock_num',0]);
                    (new \yii\db\Query())->andWhere(['!=','batch_num','']);
                    $goods_list = $goods_data->one();
                    if(empty($goods_list)){
                        $goods_name_arr[] = $order_goods['goods_name'][$i];
                    }
                }
                $goods_name_str = implode(',', $goods_name_arr);
                if(!empty($goods_name_str)){
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                    echo "<script>alert('所选仓库中没有下列商品：".$goods_name_str."');location.href='';</script>";
                    exit;
                }
            }

            $model->order_no = $order['order_no'];
            $model->store_id = $order['store_id'];
            $model->store_name = $order['store_name'];
            $model->warehouse_id = $order['warehouse_id'];
            $model->warehouse_name = $order['warehouse_name'];
            $model->shop_id = $order['shop_id'];
            $model->shop_name = ($order['shop_name']!='请选择') ? $order['shop_name']:'';
            $model->real_pay = $order['real_pay'];
            $model->discount = !empty($order['discount']) ? $order['discount'] : 0;
            $model->address_id = $order['address_id'];
            $model->remark = $order['remark'];
            $model->delivery_id = !empty($order['delivery_id']) ? $order['delivery_id'] : 0;
            $model->delivery_name = ($order['delivery_name']!='请选择') ? $order['delivery_name']:'';
            $model->create_time = $order['create_time'];
            $model->add_user_id = $order['add_user_id'];
            $model->add_user_name = $order['add_user_name'];
            $model->customer_id = $order['customer_id'];
            $model->customer_name = $order['customer_name'];
            $model->sale_time = strtotime($order['sale_time']);

            if($model->save()){
                for($i=0; $i<$count; $i++){
                    yii::$app->db->createCommand()
                        ->insert(Yii::$app->getDb()->tablePrefix.'order_goods', [
                            'order_id' => $model->attributes['order_id'],
                            'goods_id' => $order_goods['goods_id'][$i],
                            'goods_name' => $order_goods['goods_name'][$i],
                            'brand_id' => $order_goods['brand_id'][$i],
                            'brand_name' => $order_goods['brand_name'][$i],
                            'spec' => $order_goods['spec'][$i],
                            'batch_num' => $order_goods['batch_num1'][$i],
                            'stocks_id' => !empty($order_goods['stocks_id'][$i]) ? $order_goods['stocks_id'][$i] : 0,
                            'sell_price' => $order_goods['sell_price'][$i],
                            'number' => $order_goods['number'][$i],
                            'unit_id' => $order_goods['unit_id'][$i],
                            'unit_name' => $order_goods['unit_name'][$i]
                        ])->execute();
                }

                //更新生成地址
                $address = Yii::$app->request->post('Address');
                yii::$app->db->createCommand()
                    ->update(Yii::$app->getDb()->tablePrefix.'address', [
                        'idcard_url' => $address['idcard_url']
                    ],'address_id='.$order['address_id'])->execute();

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

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\bootstrap\ActiveForm::validate($model);
            }

            $order = Yii::$app->request->post('Order');
            $order_goods = Yii::$app->request->post('OrderGoods');
            $count = count($order_goods['goods_id']);

            //验证仓库是否存在商品
            if(!empty($order['warehouse_id'])){
                $wh = '';
                $store_id = Yii::$app->user->identity->store_id;
                if($store_id>0){
                    $wh['store_id'] = $store_id;
                }
                $wh['warehouse_id'] = $order['warehouse_id'];
                $goods_name_arr = array();
                for($i=0; $i<$count; $i++){
                    $wh['goods_id'] = $order_goods['goods_id'][$i];
                    $goods_data = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'stocks')
                        ->select('goods_id,goods_name')
                        ->where($wh)->orderBy(['purchase_time'=>SORT_DESC]);
                    (new \yii\db\Query())->andWhere(['>','stock_num',0]);
                    (new \yii\db\Query())->andWhere(['!=','batch_num','']);
                    $goods_list = $goods_data->one();
                    if(empty($goods_list)){
                        $goods_name_arr[] = $order_goods['goods_name'][$i];
                    }
                }
                $goods_name_str = implode(',', $goods_name_arr);
                if(!empty($goods_name_str)){
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                    echo "<script>alert('所选仓库中没有下列商品：".$goods_name_str."');location.href='';</script>";
                    exit;
                }
            }

            $model->order_no = $order['order_no'];
            $model->warehouse_id = $order['warehouse_id'];
            $model->warehouse_name = $order['warehouse_name'];
            $model->shop_id = $order['shop_id'];
            $model->shop_name = ($order['shop_name']!='请选择') ? $order['shop_name']:'';
            $model->real_pay = $order['real_pay'];
            $model->discount = !empty($order['discount']) ? $order['discount'] : 0;
            $model->address_id = $order['address_id'];
            $model->remark = $order['remark'];
            $model->delivery_id = !empty($order['delivery_id']) ? $order['delivery_id'] : 0;
            $model->delivery_name = ($order['delivery_name']!='请选择') ? $order['delivery_name']:'';
            $model->customer_id = $order['customer_id'];
            $model->customer_name = $order['customer_name'];
            $model->sale_time = strtotime($order['sale_time']);

            if($model->save()){

                yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'order_goods','order_id='.$id)->execute();
                for($i=0; $i<$count; $i++){
                    yii::$app->db->createCommand()
                        ->insert(Yii::$app->getDb()->tablePrefix.'order_goods', [
                            'id'    => $order_goods['id'][$i],
                            'order_id' => $model->attributes['order_id'],
                            'goods_id' => $order_goods['goods_id'][$i],
                            'goods_name' => $order_goods['goods_name'][$i],
                            'brand_id' => $order_goods['brand_id'][$i],
                            'brand_name' => $order_goods['brand_name'][$i],
                            'spec' => $order_goods['spec'][$i],
                            'batch_num' => $order_goods['batch_num1'][$i],
                            'stocks_id' => !empty($order_goods['stocks_id'][$i]) ? $order_goods['stocks_id'][$i] : 0,
                            'sell_price' => $order_goods['sell_price'][$i],
                            'number' => $order_goods['number'][$i],
                            'unit_id' => $order_goods['unit_id'][$i],
                            'unit_name' => $order_goods['unit_name'][$i]
                        ])->execute();
                }

            }
            //更新生成地址
            $address = Yii::$app->request->post('Address');
            yii::$app->db->createCommand()
                ->update(Yii::$app->getDb()->tablePrefix.'address', [
                    'idcard_url' => $address['idcard_url']
                ],'address_id='.$order['address_id'])->execute();

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
        if (($og_model = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'order_goods')->where('order_id='.$id)->orderBy(['id'=>SORT_ASC])->all()) !== null) {
            return $og_model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
