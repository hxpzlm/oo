<?php
namespace frontend\controllers;
use frontend\models\Check;
use frontend\models\Moving;
use frontend\models\Order;
use frontend\models\Other;
use frontend\models\Purchase;
use frontend\models\RefuseOrder;
use Yii;
use common\models\LoginForm;
use yii\helpers\Url;

/**
 * Site controller
 */
class SiteController extends CommonController
{
    /**
     * @inheritdoc
     */
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
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $store_id="";$p=0;$s=0;
        if(Yii::$app->user->identity->store_id>0) $store_id=' and store_id='.Yii::$app->user->identity->store_id;
        $pdate[]= Purchase::find()->select('buy_time as time,totle_price,purchase_id,warehouse_name')->where('purchases_status=0'.$store_id)->orderBy(['time'=>SORT_DESC])->asArray()->limit(10)->all();
        $p=$p+Purchase::find()->where('purchases_status=0'.$store_id)->count();
        $pdate[]=Order::find()->select('sale_time as time,real_pay,order_id,order_no,warehouse_name')->where('delivery_status=0'.$store_id)->asArray()->orderBy(['time'=>SORT_DESC])->limit(10)->all();
        $p=$p+Order::find()->where('delivery_status=0'.$store_id)->count();
        $pdate[]=RefuseOrder::find()->select('refuse_time as time,order_no,sale_time,refuse_id,warehouse_id,warehouse_name,refuse_amount')->where('status=0'.$store_id)->asArray()->orderBy(['time'=>SORT_DESC])->limit(10)->all();
        $p=$p+RefuseOrder::find()->where('status=0'.$store_id)->count();
        $cdata[]=Moving::find()->select('update_time as time,moving_id,to_warehouse_name,from_warehouse_name,goods_name,spec,number,unit_name')->where('status=0'.$store_id)->asArray()->orderBy(['time'=>SORT_DESC])->limit(10)->all();
        $s=$s+Moving::find()->where('status=0'.$store_id)->count();
        $cdata[]=Check::find()->select('create_time as time,check_id,warehouse_name,check_no,add_user_name')->where('status=0'.$store_id)->asArray()->orderBy(['time'=>SORT_DESC])->limit(10)->all();
        $s=$s+Check::find()->where('status=0'.$store_id)->count();
        $cdata[]=Other::find()->select('create_time as time,other_id,add_user_name')->where('status=0'.$store_id)->asArray()->orderBy(['time'=>SORT_DESC])->limit(10)->all();
        $s=$s+Other::find()->where('status=0'.$store_id)->count();
        $data=$this->arrSort($pdate);
        $other = $this->arrSort($cdata);
        $sdata = $this->insertSort($data);
        $others = $this->insertSort($other);
        krsort($sdata);
        krsort($others);
        return $this->render('index',[
            'data'=>$sdata,
            'p'=>$p,
            's'=>$s,
            'other' =>$others
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()){
			$model->loginTime(Yii::$app->user->identity->id);
         //   return $this->goBack();
            $auth = Yii::$app->authManager;
            if($auth->checkAccess(Yii::$app->user->identity->id,'site/index')){
                return $this->goBack();
            }else{
                $promission = $auth->getPermissionsByUser(Yii::$app->user->identity->id);
                return $this->redirect(Url::to([key($promission)]));
            }
        } else {
			$this->layout = "login_layout";
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('修改成功', '查看邮件进行密码重置');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('修改失败', '对不起,我们不能对你提供的E-mail进行重置密码');
            }
        }

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
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('修改成功', '新密码已保存');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    private function insertSort($arr) {
        $len=count($arr);
        for($i=1; $i<$len; $i++) {
            $tmp = $arr[$i];
            //内层循环控制，比较并插入
            for($j=$i-1;$j>=0;$j--) {
                if($tmp['time']< $arr[$j]['time']) {
                    //发现插入的元素要小，交换位置，将后边的元素与前面的元素互换
                    $arr[$j+1] = $arr[$j];
                    $arr[$j] = $tmp;
                } else {
                    break;
                }
            }
        }
        return $arr;
    }

    private function arrSort($pdate){
        $data=array();
        foreach ($pdate as $value){
            foreach ($value as $val){
                $data[]=$val;
            }
        }
        return $data;
    }
}
