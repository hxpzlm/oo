<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Expressway;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * ExpresswayController implements the CRUD actions for Expressway model.
 */
class ExpresswayController extends CommonController
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
    {$model = new Expressway();
        $s_con = Yii::$app->request->queryParams;
        $user= Yii::$app->user->identity;
        $query = new \yii\db\Query;
        $where= array();
        if($user->store_id>0) $where['store_id']=$user->store_id;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query->select('*')->from($tablePrefix.'expressway')->where($where)->orderBy(['sort'=>SORT_ASC,'create_time'=>SORT_DESC]);
        if(!empty($s_con['name'])) $query->andFilterWhere(['like','name',$s_con['name']]);
        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);
        $countries = $query->offset($pagination->offset)
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
        $model = new Expressway();
        $model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->delivery_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->delivery_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
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
        if (($model = Expressway::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
