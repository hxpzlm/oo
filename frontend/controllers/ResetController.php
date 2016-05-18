<?php
/**
 * Created by Xiegao  忘记密码
 * User: Administrator
 * Date: 2016/3/27
 * Time: 12:03
 */
namespace frontend\controllers;
use common\models\User;
use frontend\components\HuyiSms;
use frontend\models\StocksModel;
use Yii;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ErrorHandler;
use yii\web\Response;


class ResetController extends Controller{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'maxLength' => 4,
                'minLength' => 4,
                'width' => 80,
                'height' =>34,
            ],
        ];
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionIndex(){
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendUserName()) {
                $user = Yii::$app->request->post('PasswordResetRequestForm');
               return $this->redirect(array('reset/sms','name'=>$user['username']));
            } else {
                Yii::$app->session->setFlash('error', '重置密码失败');
            }
        }
        $this->layout = "login_layout";
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionPwd()
    {
        $token = Yii::$app->request->get('token');
        if(!User::isPasswordResetTokenValid($token)){
            throw  new BadRequestHttpException("重置token超时无效");
        }
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', '新密码保存成功');
            return $this->goHome();
        }
        $this->layout  = "login_layout";
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    //验证身份
    public  function actionSms(){
        $username = Yii::$app->request->get('name');
        $model = new PasswordResetRequestForm();
        $mobile =$model->getMobile($username);
        if(Yii::$app->request->isPost){
            $code = Yii::$app->request->post('phoneVerificationCode');
            $sms = Yii::$app->session->get('smsCode');
            if($code==$sms){
                $user = User::findByUsername($username);
                return $this->redirect(['pwd','token'=>$user['password_reset_token']]);
            }
        }
        $this->layout = "login_layout";
        return $this->render('sms',['mobile'=>$mobile]);

    }

    public function actionSend(){
        if(Yii::$app->request->isAjax){
            $mobile = Yii::$app->request->post('mobile');
            $obj = new HuyiSms();
            Yii::$app->response->format= Response::FORMAT_JSON;
            return $obj->send($mobile);
        }
    }
	
	public function actionPhone(){
        if(Yii::$app->request->isAjax){
             $mobile = Yii::$app->request->post('mobile');
             if($user= User::findOne(['mobile'=>$mobile])){
			 $data= ['status'=>1,'user_id'=>$user['user_id']];
			 }else{
				 $data= ['status'=>0];
			 }
			 
			 Yii::$app->response->format= Response::FORMAT_JSON;
			 
			 return $data;
        }
    }
	
	public function actionGetname(){
        if(Yii::$app->request->isAjax){
             $username = Yii::$app->request->post('username');
             if($user= User::findOne(['username'=>$username])){
			 $data= ['status'=>1,'user_id'=>$user['user_id']];
			 }else{
				 $data= ['status'=>0];
			 }
			 Yii::$app->response->format= Response::FORMAT_JSON;
			 
			 return $data;
        }
    }
	
	

    public  function actionSearch()
    {
           $wh['goods_id'] = Yii::$app->request->get('goods_id');
           $wh['warehouse_id'] = Yii::$app->request->get('warehouse_id');
           $store_id = Yii::$app->user->identity->store_id;
            if($store_id>0){
                $wh['store_id'] = Yii::$app->user->identity->store_id;
            }
            $stocks = StocksModel::find()->select('batch_num')->where($wh)->all();
            foreach($stocks as $k=>$v){
                $data[]= array('title'=>$v['batch_num']);
            }
            echo json_encode(array('data'=>$data));
    }
    public  function actionSearchsto()
    {
        if(Yii::$app->request->isAjax) {
            $wh['goods_id'] = Yii::$app->request->post('goods_id');
            $wh['warehouse_id'] = Yii::$app->request->post('warehouse_id');
            $store_id = Yii::$app->user->identity->store_id;
            if ($store_id > 0) {
                $wh['store_id'] = Yii::$app->user->identity->store_id;
            }
            $stocks = StocksModel::find()->select('batch_num')->where($wh)->all();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $stocks;
        }
    }

}


