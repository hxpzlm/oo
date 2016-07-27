<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Check;
use frontend\models\CheckSearch;
use frontend\models\CheckGoods;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\filters\AccessControl;

/**
 * CheckController implements the CRUD actions for Check model.
 */
class CheckController extends CommonController
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
     * Lists all Check models.
     * @return mixed
     */
    public function actionIndex()
    {
        //清楚session
        unset(Yii::$app->session['check_step1']);
        unset(Yii::$app->session['check_step2']);

        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $searchModel = new CheckSearch();
        $s_con = Yii::$app->request->queryParams;
        //搜索条件
        $create_time_start = strtotime(Yii::$app->request->get('create_time_start')); //开单开始日期
        $create_time_end = strtotime(Yii::$app->request->get('create_time_end').' 23:59:59'); //开单结束日期
        $where='';
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['store_id'] = $store_id;
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['store_id'] = $sid;
            }
        }
        if(isset($s_con['status'])){ //盘点状态
            if($s_con['status']==1)  $where['status'] = $s_con['status'];
            if($s_con['status']==2)  $where['status'] = 0;
        }

        $check = (new \yii\db\Query())->from($tablePrefix.'check')
            ->select('*')
            ->where($where)->orderBy(['create_time'=>SORT_DESC]);
        if (!empty($s_con['warehouse_name'])) $check->andFilterWhere(['like', 'warehouse_name',$s_con['warehouse_name']]); //仓库
        if (!empty($s_con['goods_name'])) $check->andFilterWhere(['like', 'goods_name',$s_con['goods_name']]); //商品名称
        if (!empty($s_con['add_user_name'])) $check->andFilterWhere(['like', 'add_user_name',$s_con['add_user_name']]); //开单人

        if($create_time_start<=$create_time_end) {
            if(!empty($create_time_start)) $check->andWhere(['>=','create_time',$create_time_start]);
            if(!empty($create_time_end)) $check->andWhere(['<=','create_time',$create_time_end]);
        }
        $countQuery = clone $check;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $check->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['仓库','开单人','开单时间', '盘点完成','确认人','确认时间']];
            foreach ($countQuery->all() as $row) {
                $final[] = [
                    $row['warehouse_name'].'盘点（'.$row['check_no'].'）',$row['add_user_name'],date('Y-m-d H:i:s', $row['create_time']),($row['status']==1)?'是':'否',$row['confirm_user_name'],date('Y-m-d H:i:s', $row['confirm_time']),
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }

        //ajax 根据仓库id
        if(Yii::$app->request->post('action')=='goods_screen'){

            $warehouse_id = Yii::$app->request->post('warehouse_id'); //仓库id
            $cat_id = Yii::$app->request->post('cid'); //分类id
            if(!empty($cat_id)){
                $map['cat_id'] = $cat_id;
            }
            if(!empty($warehouse_id)){
                $map['warehouse_id'] = $warehouse_id;
                $stocks_data = (new \yii\db\Query())->select('*')->from($tablePrefix.'stocks')->where($map)->orderBy(['purchase_time'=>SORT_DESC]);
                $stocks_data->andWhere(['>','stock_num',0]);
                $stocks_data->andWhere(['!=','batch_num','']);
                $goods_list = $stocks_data->groupBy(['goods_id'])->all();

                die(json_encode($goods_list));
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Displays a single Check model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Check model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Check();
        $model_goods = new CheckGoods();
        $act = Yii::$app->request->post('act');

        if(Yii::$app->request->post()){
            $check = Yii::$app->request->post('Check');
            $check_goods = Yii::$app->request->post('CheckGoods');
            //第一步
            if($act=='check_1'){
                if ($model->load(Yii::$app->request->post())) {
                    $cg = array();
                    for($i=0; $i<count($check_goods['goods_id']); $i++){
                        $cg[$i]['goods_id'] =  $check_goods['goods_id'][$i];
                        $cg[$i]['goods_name'] =  $check_goods['goods_name'][$i];
                        $cg[$i]['spec'] =  $check_goods['spec'][$i];
                        $cg[$i]['unit_id'] =  $check_goods['unit_id'][$i];
                        $cg[$i]['unit_name'] =  $check_goods['unit_name'][$i];
                    }
                    Yii::$app->session['check_step1']=array(
                        'warehouse_id'=> $check['warehouse_id'],
                        'warehouse_name'=> $check['warehouse_name'],
                        'cat_id'=> $check['cat_id'],
                        'cg'=> $cg,
                    );
                    return $this->redirect(['create', 'step' => 2]);
                }

            }else if($act=='check_2'){

                foreach($check_goods['batch_num'] as $k=>$v){
                    $s_map['goods_id'] = $k;
                    $s_map['batch_num'] = $check_goods['batch_num'][$k];
                    $stocks_data = (new \yii\db\Query())->select('unit_id,unit_name,batch_num,stock_num,store_id,store_name')->from(Yii::$app->getDb()->tablePrefix.'stocks')->where($s_map)->orderBy(['purchase_time'=>SORT_DESC]);
                    $stocks_data->andWhere(['>','stock_num',0]);
                    $r_stocks[$k] = $stocks_data->all();
                }
                Yii::$app->session['check_step2']=$r_stocks;
                return $this->redirect(['create', 'step' => 3]);

            }else if($act=='check_3'){

                $model->check_no = 'PDD'.date('YmdHisu', time());
                $model->warehouse_id = Yii::$app->session['check_step1']['warehouse_id'];
                $model->warehouse_name = Yii::$app->session['check_step1']['warehouse_name'];
                $model->store_id = Yii::$app->user->identity->store_id;
                $model->store_name = Yii::$app->user->identity->store_name;
                $model->cat_id = Yii::$app->session['check_step1']['cat_id'];
                $model->add_user_id = Yii::$app->user->id;
                $model->add_user_name = Yii::$app->user->identity->add_user_name;
                $model->create_time = time();

                if($model->save()){
                    if ($model_goods->load(Yii::$app->request->post())) {
                        $cg = Yii::$app->session['check_step1']['cg'];
                        if(!empty($cg)){
                            foreach($cg as $row){
                                $model_goods->check_id = $model->attributes['check_id'];
                                $model_goods->goods_id = $row['goods_id'];
                                $model_goods->goods_name = $row['goods_name'];
                                $model_goods->spec = $row['spec'];
                                $model_goods->unit_id = $row['unit_id'];
                                $model_goods->unit_name = $row['unit_name'];

                                $model_goods->batch_num = implode(',', $check_goods['batch_num'][$row['goods_id']]);
                                $model_goods->stocks_num = implode(',', $check_goods['stocks_num'][$row['goods_id']]);
                                $model_goods->check_num = implode(',', $check_goods['check_num'][$row['goods_id']]);
                                $model_goods->remark = implode(',', $check_goods['remark'][$row['goods_id']]);

                                $model_goods->save();
                            }

                        }
                        //清楚session
                        unset(Yii::$app->session['check_step1']);
                        unset(Yii::$app->session['check_step2']);
                        return $this->redirect(['view', 'id' => $model->check_id]);
                    }
                }

            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Check model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $cg_model = $this->findCGModel($id);
        $model_goods = new CheckGoods();
        if(Yii::$app->request->post()){
            $act = Yii::$app->request->post('act');
            $check = Yii::$app->request->post('Check');
            $check_goods = Yii::$app->request->post('CheckGoods');
            //第一步
            if($act=='check_1'){
                if ($model->load(Yii::$app->request->post())) {
                    $cg = array();
                    for($i=0; $i<count($check_goods['goods_id']); $i++){
                        $cg[$i]['id'] =  $check_goods['id'][$i];
                        $cg[$i]['goods_id'] =  $check_goods['goods_id'][$i];
                        $cg[$i]['goods_name'] =  $check_goods['goods_name'][$i];
                        $cg[$i]['spec'] =  $check_goods['spec'][$i];
                        $cg[$i]['unit_id'] =  $check_goods['unit_id'][$i];
                        $cg[$i]['unit_name'] =  $check_goods['unit_name'][$i];
                    }
                    Yii::$app->session['check_step1']=array(
                        'warehouse_id'=> $check['warehouse_id'],
                        'warehouse_name'=> $check['warehouse_name'],
                        'cat_id'=> $check['cat_id'],
                        'cg'=> $cg,
                    );
                    return $this->redirect(['update', 'id' =>$id, 'step' => 2]);
                }

            }else if($act=='check_2'){

                foreach($check_goods['batch_num'] as $k=>$v){
                    $s_map['goods_id'] = $k;
                    $s_map['batch_num'] = $check_goods['batch_num'][$k];
                    $stocks_data = (new \yii\db\Query())->select('unit_id,unit_name,batch_num,stock_num,store_id,store_name')->from(Yii::$app->getDb()->tablePrefix.'stocks')->where($s_map)->orderBy(['purchase_time'=>SORT_DESC]);
                    $stocks_data->andWhere(['>','stock_num',0]);
                    $r_stocks[$k] = $stocks_data->all();
                }
                Yii::$app->session['check_step2']=$r_stocks;

                return $this->redirect(['update', 'id' =>$id, 'step' => 3]);

            }else if($act=='check_3'){

                $model->check_no = 'PDD'.date('YmdHisu', time());
                $model->warehouse_id = Yii::$app->session['check_step1']['warehouse_id'];
                $model->warehouse_name = Yii::$app->session['check_step1']['warehouse_name'];
                $model->store_id = Yii::$app->user->identity->store_id;
                $model->store_name = Yii::$app->user->identity->store_name;
                $model->cat_id = Yii::$app->session['check_step1']['cat_id'];
                $model->add_user_id = Yii::$app->user->id;
                $model->add_user_name = Yii::$app->user->identity->add_user_name;
                $model->create_time = time();

                if($model->save()){
                    if ($model_goods->load(Yii::$app->request->post())) {
                        yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'check_goods','check_id='.$id)->execute();//删除历史数据
                        $cg = Yii::$app->session['check_step1']['cg'];

                        if(!empty($cg)){
                            foreach($cg as $row){
                                yii::$app->db->createCommand()
                                    ->insert(Yii::$app->getDb()->tablePrefix.'check_goods', [
                                        'id'    => $row['id'],
                                        'check_id' => $model->attributes['check_id'],
                                        'goods_id' => $row['goods_id'],
                                        'goods_name' => $row['goods_name'],
                                        'spec' => $row['spec'],
                                        'unit_id' => $row['unit_id'],
                                        'unit_name' => $row['unit_name'],
                                        'batch_num' => implode(',', $check_goods['batch_num'][$row['goods_id']]),
                                        'stocks_num' => implode(',', $check_goods['stocks_num'][$row['goods_id']]),
                                        'check_num' => implode(',', $check_goods['check_num'][$row['goods_id']]),
                                        'remark' => implode(',', $check_goods['remark'][$row['goods_id']]),
                                    ])->execute();

                            }

                        }
                        //清楚session
                        unset(Yii::$app->session['check_step1']);
                        unset(Yii::$app->session['check_step2']);
                        return $this->redirect(['view', 'id' => $model->check_id]);
                    }
                }

            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'cg_model'  => $cg_model,
            ]);
        }
    }

    /**
     * Updates an existing Check model.
     * If update is successful, the browser will be redirected to the 'handle' page.
     * @param integer $id
     * @return mixed
     */
    public function actionHandle($id)
    {
        $model = $this->findModel($id);
        $p = Yii::$app->request->get('page');
        $model->status = 1;//已盘点
        $model->confirm_user_id = Yii::$app->user->id; //审核人id
        $model->confirm_user_name = Yii::$app->user->identity->real_name; //审核人真实姓名
        $model->confirm_time = time(); //审核时间

        if ($model->save()) {
            return $this->redirect(['index','page'=>$p]);
        }

    }

    /**
     * Deletes an existing Check model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'check_goods','check_id='.$id)->execute();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Check model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Check the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Check::findOne($id)) !== null) {
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
    protected function findCGModel($id)
    {
        if (($cg_model = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'check_goods')->where('check_id='.$id)->orderBy(['id'=>SORT_ASC])->all()) !== null) {
            return $cg_model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
