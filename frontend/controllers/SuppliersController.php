<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Suppliers;
use frontend\models\SuppliersSearch;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SuppliersController extends CommonController
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
     * Lists all Suppliers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SuppliersSearch();
        $s_con = Yii::$app->request->queryParams;
        $name = !empty($s_con['SuppliersSearch']['name']) ? $s_con['SuppliersSearch'] : '';
        $query = Suppliers::find();
        if (!empty($name)) $query->andFilterWhere(['like', 'name',$name]); //供应商名称
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->orderBy(['sort'=>SORT_ASC,'suppliers_id'=>SORT_DESC])->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        //更新排序
        if(Yii::$app->request->post('sort')){
            foreach(Yii::$app->request->post('sort') as $k => $v)
            {
                $model = $this->findModel($k);
                $model->sort = $v;
                if($model->save()){
                    $this->redirect(['index']);
                }
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Displays a single Suppliers model.
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
     * Creates a new Suppliers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Suppliers();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->suppliers_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Suppliers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
          //  var_dump(Yii::$app->request->post());exit;
                $model->save();

            return $this->redirect(['view', 'id' => $model->suppliers_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Suppliers model.
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
     * Finds the Suppliers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Suppliers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Suppliers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
