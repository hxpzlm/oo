<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Unit;
use frontend\models\UnitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends CommonController
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
     * Lists all Unit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Unit();
        $s_con = Yii::$app->request->queryParams;
        $where = array();
        $query = new \yii\db\Query;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query->select('unit_id,sort,unit,remark')->from($tablePrefix.'unit')->where($where)->orderBy(['sort'=>SORT_ASC]);
        if(!empty($s_con['unit'])) $query->andFilterWhere(['like','unit',$s_con['unit']]);
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
            'searchModel' => $searchModel,
            'countries' => $countries,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Unit model.
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
     * Creates a new Unit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Unit();
        $model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->unit_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Unit model.
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
            return $this->redirect(['index', 'id' => $model->unit_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Unit model.
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
     * Finds the Unit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Unit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Unit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
