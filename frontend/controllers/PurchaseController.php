<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Purchase;
use frontend\models\PurchaseSearch;
use frontend\models\PurchaseGoods;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PurchaseController implements the CRUD actions for purchase model.
 */
class PurchaseController extends CommonController
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
     * Lists all purchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $searchModel = new PurchaseSearch();
        $s_con = Yii::$app->request->queryParams;

        //搜索条件
        $buy_time_start = strtotime(Yii::$app->request->get('buy_time_start')); //采购开始时间
        $buy_time_end = strtotime(Yii::$app->request->get('buy_time_end')); //采购结束时间
        $where='';
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['p.store_id'] = $store_id;
        }
        if (!empty($s_con['warehouse_id'])) $where['p.warehouse_id'] = $s_con['warehouse_id']; //仓库
        if (!empty($s_con['purchases_status'])) $where['p.purchases_status'] = $s_con['purchases_status']; //入库状态
        if (!empty($s_con['principal_id'])) $where['p.principal_id'] = $s_con['principal_id']; //负责人

        $query = (new \yii\db\Query())->from($tablePrefix.'purchase as p')
            ->select('p.purchase_id,p.warehouse_name,g.name,g.brand_name,g.barode_code,pg.buy_price,pg.number,pg.goods_name,p.batch_num,p.buy_time,pg.supplier_name,pg.spec,p.purchases_status')
            ->leftJoin(['pg' => $tablePrefix.'purchase_goods'],'p.purchase_id = pg.purchase_id')
            ->leftJoin(['g' => $tablePrefix.'goods'],'pg.goods_id = g.goods_id')
            ->where($where)->orderBy(['p.purchase_id'=>SORT_DESC]);
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'pg.goods_name',$s_con['goods_name']]); //商品中英文
        if (!empty($s_con['barode_code'])) $query->andFilterWhere(['like', 'pg.barode_code',$s_con['barode_code']]); //条形码
        if (!empty($s_con['brand_name'])) $query->andFilterWhere(['like', 'pg.brand_name',$s_con['brand_name']]); //商品品牌
        if (!empty($s_con['supplier_name'])) $query->andFilterWhere(['like', 'pg.supplier_name',$s_con['supplier_name']]); //供应商
        if($buy_time_start<=$buy_time_end){
            if(!empty($buy_time_start)) $query->andWhere(['>=','p.buy_time',$buy_time_start]);
            if(!empty($buy_time_end))  $query->andWhere(['<=','p.buy_time',$buy_time_end]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>10]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['仓库','商品中英文名称（含规格）','品牌', '条形码','采购单价','采收数量','批号','采购时间','供应商','入库状态']];
            foreach ($dataProvider as $row) {

                $final[] = [
                    $row['warehouse_name'],$row['name'],$row['brand_name'],$row['barode_code']."\t",$row['buy_price'],$row['number'],$row['batch_num']."\t",date('Y-m-d',$row['buy_time']),$row['supplier_name'],($row['purchases_status']==0)?"否":"是",
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Displays a single purchase model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'model_pg'  => $this->findPgModel($id),
        ]);
    }

    /**
     * Creates a new purchase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Purchase();
        $model_pg = new PurchaseGoods();

        $purchase = Yii::$app->request->post('Purchase');
        //插入采购订单表
        $model->store_id   = $purchase['store_id'];
        $model->store_name = $purchase['store_name'];
        $model->warehouse_id = $purchase['warehouse_id'];
        $model->warehouse_name = $purchase['warehouse_name'];
        $model->create_time = $purchase['create_time'];
        $model->buy_time = strtotime($purchase['buy_time']);
        $model->add_user_id = $purchase['add_user_id'];
        $model->add_user_name = $purchase['add_user_name'];
        $model->principal_id = $purchase['principal_id'];
        $model->principal_name = $purchase['principal_name'];
        $model->invoice_and_pay_sate = $purchase['invoice_and_pay_sate'];
        $model->remark = $purchase['remark'];
        $model->batch_num = $purchase['batch_num'];
        $model->invalid_time = strtotime($purchase['invalid_time']);
        $model->totle_price = $purchase['totle_price'];
        if ($model->save()) {
            //插入采购商品表
            $purchasegoods = Yii::$app->request->post('PurchaseGoods');

            $model_pg->purchase_id = $model->attributes['purchase_id'];
            $model_pg->goods_id = $purchasegoods['goods_id'];
            $model_pg->goods_name = $purchasegoods['goods_name'];
            $model_pg->spec = $purchasegoods['spec'];
            $model_pg->brand_id = $purchasegoods['brand_id'];
            $model_pg->brand_name = $purchasegoods['brand_name'];
            $model_pg->barode_code = $purchasegoods['barode_code'];
            $model_pg->unit_id = $purchasegoods['unit_id'];
            $model_pg->unit_name = $purchasegoods['unit_name'];
            $model_pg->buy_price = $purchasegoods['buy_price'];
            $model_pg->number = $purchasegoods['number'];
            $model_pg->supplier_id = $purchasegoods['supplier_id'];
            $model_pg->supplier_name = $purchasegoods['supplier_name'];

            if($model_pg->save()){
                return $this->redirect(['index']);
            }

        } else {

            return $this->render('create', [
                'model' => $model,
                'model_pg' => $model_pg,
            ]);
        }
    }

    /**
     * Updates an existing purchase model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_pg = $this->findPgModel($id);

        //更新采购表
        if ($model->load(Yii::$app->request->post())) {
            $purchase = Yii::$app->request->post('Purchase');
            $model->invalid_time = strtotime($purchase['invalid_time']);
            $model->buy_time = strtotime($purchase['buy_time']);

            if($model->save()){
                //更新采购商品表
                $purchasegoods = Yii::$app->request->post('PurchaseGoods');

                $model_pg->purchase_id = $model->attributes['purchase_id'];
                $model_pg->goods_id = $purchasegoods['goods_id'];
                $model_pg->goods_name = $purchasegoods['goods_name'];
                $model_pg->spec = $purchasegoods['spec'];
                $model_pg->brand_id = $purchasegoods['brand_id'];
                $model_pg->brand_name = $purchasegoods['brand_name'];
                $model_pg->barode_code = $purchasegoods['barode_code'];
                $model_pg->unit_id = $purchasegoods['unit_id'];
                $model_pg->unit_name = $purchasegoods['unit_name'];
                $model_pg->buy_price = $purchasegoods['buy_price'];
                $model_pg->number = $purchasegoods['number'];
                $model_pg->supplier_id = $purchasegoods['supplier_id'];
                $model_pg->supplier_name = $purchasegoods['supplier_name'];
            }

            if ($model_pg->save()) {
                return $this->redirect(['index']);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'model_pg' => $model_pg,
            ]);
        }
    }

    /**
     * Deletes an existing purchase model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        yii::$app->db->createCommand()->delete(Yii::$app->getDb()->tablePrefix.'purchase_goods','purchase_id='.$id)->execute();

        return $this->redirect(['index']);
    }

    /**
     * Finds the purchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return purchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = purchase::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the purchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return purchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPgModel($id)
    {
        if (($model_pg = PurchaseGoods::findOne(['purchase_id'=>$id])) !== null) {
            return $model_pg;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
