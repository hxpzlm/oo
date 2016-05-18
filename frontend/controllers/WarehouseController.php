<?php

namespace frontend\controllers;

use Yii;
use frontend\models\WarehouseModel;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * ExpresswayController implements the CRUD actions for Expressway model.
 */
class WarehouseController extends CommonController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Expressway models.
     * @return mixed
     */
    public function actionIndex()
    {$model = new WarehouseModel();
        $s_con = Yii::$app->request->queryParams;
        $user= Yii::$app->user->identity;
        $query = new \yii\db\Query;
        $where= array();
        if($user->store_id>0) $where['store_id']=$user->store_id;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query->select('*')->from($tablePrefix.'warehouse')->where($where)->orderBy(['sort'=>SORT_ASC]);
        if(!empty($s_con['name'])) $query->andFilterWhere(['like','name',$s_con['name']]);
        $pagination = new Pagination([
            'defaultPageSize' => 15,
            'totalCount' => $query->count(),
        ]);
        $countries = $query->orderBy(['sort'=>SORT_ASC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        if(Yii::$app->request->post('sort')){
            foreach(Yii::$app->request->post('sort') as $k => $v)
            {
                $model = $this->findModel($k);
                $model->sort = $v;

                if($model->save()){
                    $this->redirect(['index']);
                }
            }
        };

        return $this->render('index', [
            'model' => $model,
            'countries' => $countries,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Expressway model.
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
     * Creates a new Expressway model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WarehouseModel();
        $model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        $user= Yii::$app->user->identity;
        $query = new \yii\db\Query;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $model->load(Yii::$app->request->post());
        if(!empty($model->principal_id)) {
            $user_username = $query->from($tablePrefix . 'user')->where('user_id=' . $model->principal_id)->one();
            $model->principal_name = $user_username['username'];
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->warehouse_id]);
        } else {
            //查负责人
            $principal=$query->select('user_id,username')->from($tablePrefix.'user')->where('status=1 and store_id='.$user->store_id)->orderBy(['sort'=>SORT_ASC])->all();
            $principal_row = array();
            $principal_row['']='请选择';
            if(!empty($principal)){
                foreach($principal as $value){
                    $principal_row[$value['user_id']] = $value['username'];
                }
            }
            return $this->render('create', [
                'model' => $model,
                'principal_row' =>$principal_row,
            ]);
        }
    }

    /**
     * Updates an existing Expressway model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        $user= Yii::$app->user->identity;
        $query = new \yii\db\Query;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if ($model->load(Yii::$app->request->post())) {
            $user_username = $query->select('username')->from($tablePrefix.'user')->where('user_id='.$model->principal_id)->one();
            $model->principal_name=$user_username['username'];
            $model->save();
            return $this->redirect(['index', 'id' => $model->warehouse_id]);
        } else {
            //查负责人
            $principal=$query->select('user_id,username')->from($tablePrefix.'user')->where('status=1 and store_id='.$user->store_id)->orderBy(['sort'=>SORT_ASC])->all();
            $principal_row = array();
            $principal_row['']='请选择';
            if(!empty($principal)){
                foreach($principal as $value){
                    $principal_row[$value['user_id']] = $value['username'];
                }
            }
            return $this->render('update', [
                'model' => $model,
                'principal_row' =>$principal_row,
            ]);
        }
    }

    /**
     * Deletes an existing Expressway model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Expressway model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expressway the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WarehouseModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
