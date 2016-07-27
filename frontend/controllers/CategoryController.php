<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends CommonController
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Category();
        $s_con=Yii::$app->request->queryParams;
        $user=Yii::$app->user->identity;
        $where = array();
        if($user->store_id>0) $where['store_id'] = $user->store_id;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query = new \yii\db\Query;
        $parent_id =0;
        if(Yii::$app->request->get('parent_id')){
            $parent_id=Yii::$app->request->get('parent_id');
        }
        $where['parent_id'] = $parent_id;
        $query->select('cat_id,name,sort,parent_id,remark,status')->from($tablePrefix.'category')->where($where)->orderBy(['sort'=>SORT_ASC,'create_time'=>SORT_DESC]);

        if(!empty($s_con['name'])) $query->andFilterWhere(['like','name',$s_con['name']]);

        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);
        //确认排序
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
        $countries = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        //ajax判断同父级别分类是否重复
        if(Yii::$app->request->post('action')=='e_cname'){
            $cw = '';
            $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $cw['store_id'] = $store_id;
            }
            $cw['parent_id'] = Yii::$app->request->post('parent_id');
            $cw['name'] = Yii::$app->request->post('name');
            $res = $query->from($tablePrefix.'category')->where($cw)->count();
            die(json_encode($res));

        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'countries' => $countries,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        /*$model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }*/
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            $parent_id=Yii::$app->request->get('parent_id');
            $parent_id = !empty($parent_id) ? $parent_id : '0';
            return $this->render('create', [
                'model' => $model,
                'parent_id'=>$parent_id,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
//        $model->load($_POST);
//        if (Yii::$app->request->isAjax) {
//            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//            return \yii\bootstrap\ActiveForm::validate($model);
//        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->cat_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
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
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
