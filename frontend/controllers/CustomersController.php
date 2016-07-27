<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Customers;
use frontend\models\CustomersSearch;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CustomersController implements the CRUD actions for Customers model.
 */
class CustomersController extends CommonController
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
     * Lists all Customers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomersSearch();
        $s_con = Yii::$app->request->queryParams;
        $username = !empty($s_con['CustomersSearch']['username']) ? $s_con['CustomersSearch'] : '';
        $query = Customers::find();
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $where['store_id'] = $store_id;
            $query->where($where);
        }

        if (!empty($username)) $query->andFilterWhere(['like', 'username',$username]); //客户帐号
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->orderBy(['sort'=>SORT_ASC,'customers_id'=>SORT_DESC])->offset($pages->offset)
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
     * Displays a single Customers model.
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
     * Creates a new Customers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customers();
        $address = (new \yii\db\ActiveRecord)->tableName();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\bootstrap\ActiveForm::validate($model);
            }

            //插入收货地址表
            if($model->save()){
                $address1 = Yii::$app->request->post('Address');

                $count = count($address1['accept_name']);
                for($i=0; $i<$count; $i++){
                    yii::$app->db->createCommand()
                        ->insert('s2_address', [
                            'customers_id' => $model->attributes['customers_id'],
                            'accept_name' => $address1['accept_name'][$i],
                            'accept_mobile' => $address1['accept_mobile'][$i],
                            'accept_address' => $address1['accept_address'][$i],
                            'accept_idcard' => $address1['accept_idcard'][$i],
                            'is_idcard' => isset($address1['is_idcard'][$i]) ? $address1['is_idcard'][$i] : 0,
                            //'idcard_url' => $address1['idcard_url'][$i],
                            'zcode' => $address1['zcode'][$i],
                            'add_user_id' => $model->add_user_id,
                            'add_user_name' => $model->add_user_name,
                            'create_time' => $model->create_time
                    ])->execute();
                }
            }

            return $this->redirect(['view', 'id' => $model->customers_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'address'   => $address,
            ]);
        }
    }

    /**
     * Updates an existing Customers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $address = $this->findAddressModel($id);
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\bootstrap\ActiveForm::validate($model);
            }

            //插入收货地址表
            if($model->save()){
                $address1 = Yii::$app->request->post('Address');

                $count = count($address1['accept_name']);
                yii::$app->db->createCommand()->delete($tablePrefix.'address','customers_id='.$id)->execute();

                for($i=0; $i<$count; $i++){

                    yii::$app->db->createCommand()
                        ->insert($tablePrefix.'address', [
                            'address_id'    => !empty($address1['address_id'][$i])?$address1['address_id'][$i]:'',
                            'customers_id' => $id,
                            'accept_name' => $address1['accept_name'][$i],
                            'accept_mobile' => $address1['accept_mobile'][$i],
                            'accept_address' => $address1['accept_address'][$i],
                            'accept_idcard' => $address1['accept_idcard'][$i],
                            'is_idcard' => isset($address1['is_idcard'][$i]) ? $address1['is_idcard'][$i] : 0,
                            'zcode' => $address1['zcode'][$i],
                            'add_user_id' => $model->add_user_id,
                            'add_user_name' => $model->add_user_name,
                            'create_time' => $model->create_time
                        ])->execute();
                }
            }
            return $this->redirect(['view', 'id' => $model->customers_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'address'   => $address,
            ]);
        }
    }

    /**
     * Deletes an existing Customers model.
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
     * Finds the Customers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Address the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findAddressModel($id)
    {
        if (($address = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'address')->where('customers_id='.$id)->orderBy(['create_time'=>SORT_ASC])->all()) !== null) {
            return $address;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
