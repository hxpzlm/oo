<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Moving;
use frontend\models\MovingSearch;
use frontend\models\StocksModel;
use frontend\models\Goods;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MovingController implements the CRUD actions for Moving model.
 */
class MovingController extends CommonController
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
     * Lists all Moving models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = new \yii\db\Query();
        $searchModel = new MovingSearch();
        $s_con = Yii::$app->request->queryParams;

        //搜索条件
        $update_time_start = strtotime(Yii::$app->request->get('update_time_start')); //调剂开始时间
        $update_time_end = strtotime(Yii::$app->request->get('update_time_end')); //调剂结束时间
        $where='';
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['store_id'] = $store_id;
        }
        //if (!empty($s_con['goods_id'])) $where['goods_id']= $s_con['goods_id']; //商品id
        //if (!empty($s_con['brand_id'])) $where['brand_id'] = $s_con['brand_id'];//品牌id

        if (!empty($s_con['from_warehouse_id'])) $where['from_warehouse_id'] = $s_con['from_warehouse_id'];//来源仓库
        if (!empty($s_con['to_warehouse_id'])) $where['to_warehouse_id'] = $s_con['to_warehouse_id']; //目标仓库

        $moving = $query->from($tablePrefix.'moving')
            ->select('moving_id,from_warehouse_name,to_warehouse_name,goods_name,brand_name,barode_code,spec,number,update_time,status,confirm_time')
            ->where($where)->orderBy(['moving_id'=>SORT_DESC]);
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'goods_name',$s_con['goods_name']]); //商品名称
        if (!empty($s_con['barode_code'])) $query->andFilterWhere(['like', 'barode_code',$s_con['barode_code']]); //条形码
        if (!empty($s_con['brand_name'])) $query->andFilterWhere(['like', 'brand_name',$s_con['brand_name']]); //条形码
        if($update_time_start<=$update_time_end) {
            if(!empty($update_time_start)) $query->andWhere(['>=','update_time',$update_time_start]);
            if(!empty($update_time_end))  $query->andWhere(['<=','update_time',$update_time_end]);
        }
        $countQuery = clone $moving;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>15]);
        $dataProvider = $moving->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['仓库','商品中英文名称（含规格）','品牌', '条形码','调剂数量','调剂日期','入库状态','入库日期']];

            foreach ($dataProvider as $row) {
                $final[] = [
                    $row['from_warehouse_name'].'->'.$row['to_warehouse_name'],$row['goods_name'].' '.$row['spec'],$row['brand_name'],$row['barode_code']."\t",$row['number'],date('Y-m-d H:i:s',$row['update_time']),$row['status']==1?'入库':'未入库',date('Y-m-d H:i:s',$row['confirm_time']),
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }
        //ajax获取所属商品下面的批次号
        if(Yii::$app->request->get('action')=='f_batch'){
            $wh = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $wh['p.store_id'] = $store_id;
            }
            $from_warehouse_id = Yii::$app->request->get('from_warehouse_id');
            if(!empty($from_warehouse_id)){
                $wh['p.warehouse_id'] = $from_warehouse_id;
            }
            $goods_id = Yii::$app->request->get('goods_id');
            if(!empty($goods_id)){
                $wh['pg.goods_id'] = $goods_id;
            }

            $batch_num = (new \yii\db\Query())->from($tablePrefix.'purchase as p')
                ->select('p.batch_num')
                ->leftJoin(['pg' => $tablePrefix.'purchase_goods'],'p.purchase_id = pg.purchase_id')
                ->where($wh)->orderBy(['p.purchase_id'=>SORT_DESC])->all();
            foreach($batch_num as $k=>$v){
                $data[]= array('title'=>$v['batch_num']);
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
     * Displays a single Moving model.
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
     * Creates a new Moving model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Moving();

        if ($model->load(Yii::$app->request->post())) {
            $moving = Yii::$app->request->post('Moving');
            $model->update_time = strtotime($moving['update_time']);
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->moving_id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Moving model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $moving = Yii::$app->request->post('Moving');
            $model->update_time = strtotime($moving['update_time']);
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->moving_id]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Moving model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionHandle($id,$action){
        $model = $this->findModel($id);
        if($action=="comfirm"){
            $bt = Yii::$app->db->beginTransaction();
            $wh['goods_id'] = $model['goods_id'];
            $wh['warehouse_id'] = $model['from_warehouse_id'];
            $wh['batch_num'] = $model['batch_num'];
            if(Yii::$app->user->identity->store_id>0){
                $wh['store_id'] = Yii::$app->user->identity->store_id;
            }
            try{
                if($stocks=StocksModel::findOne($wh)){
                    $stocks->stock_num= $stocks->stock_num-$model['number'];

                    if(!$stocks->save()){
                        throw new \Exception('调剂商品入库失败');
                    }
                }else{
                    throw new \Exception('来源仓库不存在该商品');
                }
                $wh['warehouse_id'] = $model['to_warehouse_id'];
                if($gf= StocksModel::findOne($wh)){
                    $gf->stock_num = $gf['stock_num']+$model['number'];
                    if(!$gf->save()){
                        throw new \Exception('调剂商品进入目标仓库失败');
                    }
                }else{
                    $ss= new StocksModel();
                    $ss->store_id = $model['store_id'];
                    $ss->store_name = $model['store_name'];
                    $ss->warehouse_id = $model['to_warehouse_id'];
                    $ss->warehouse_name = $model['to_warehouse_name'];
                    $ss->goods_id = $model['goods_id'];
                    $ss->goods_name = $model['goods_name'];
                    $ss->brand_id = $model['brand_id'];
                    $ss->brand_name = $model['brand_name'];
                    $ss->barode_code = $model['barode_code'];
                    $goods = Goods::findOne(['goods_id'=>$model['goods_id'],'store_id'=>$model['store_id']]);
                    $ss->cat_id = $goods['cat_id'];
                    $ss->cat_name = $goods['cat_name'];
                    $ss->unit_id = $model['unit_id'];
                    $ss->unit_name = $model['unit_name'];
                    $ss->spec = $model['spec'];
                    $ss->batch_num  = $model['batch_num'];
                    $ss->stock_num  = $model['number'];
                    $ss->purchase_num  = $model['number'];
                    $ss->purchase_time = $stocks->purchase_time;
                    $ss->moving_id  = $id;

                    if(!$ss->save()){
                        throw new \Exception("调剂商品入库失败");
                    }
                }

                $model->confirm_time = time();
                $model->confirm_user_id = Yii::$app->user->id;
                $model->confirm_user_name = Yii::$app->user->identity->real_name;
                $model->status = 1;
                if(!$model->save()){
                    throw new \Exception("确认调剂入库失败");
                }
                $bt->commit();
            }catch(Exception $e){
                $bt->rollBack();
            }
        }
        if($action=="cancle"){
            $bt = Yii::$app->db->beginTransaction();
            $wh['goods_id'] = $model['goods_id'];
            $wh['warehouse_id'] = $model['from_warehouse_id'];
            $wh['batch_num'] = $model['batch_num'];
            if(Yii::$app->user->identity->store_id>0){
                $wh['store_id'] = Yii::$app->user->identity->store_id;
            }
            try{
                if($st=StocksModel::findOne($wh)){
                    $st->stock_num= $st['stock_num']+$model['number'];
                    if(!$st->save()){
                        throw new \Exception('取消调剂商品入库失败');
                    }
                }else{
                    throw new \Exception('该仓库不存在该商品');
                }

                if(StocksModel::findOne(['moving_id'=>$id])){
                    StocksModel::deleteAll(['moving_id'=>$id]);
                }

                $wh['warehouse_id'] = $model['to_warehouse_id'];
                if($ss = StocksModel::findOne($wh)){
                    if($ss['stock_num']>$model['number']){
                        $ss->stock_num = $ss['stock_num']-$model['number'];
                    }else{
                        $ss->stock_num = "0";
                    }
                    if(!$ss->save()){
                        throw new \Exception("目标仓库取消调剂入库失败");
                    }
                }
                $model->confirm_time = 0;
                $model->confirm_user_id = 0;
                $model->confirm_user_name = "";
                $model->status = 0;
                if(!$model->save()){
                    throw new \Exception("取消调剂入库失败");
                }
                $bt->commit();
            }catch(Exception $e){
                $bt->rollBack();
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Moving model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Moving the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Moving::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
