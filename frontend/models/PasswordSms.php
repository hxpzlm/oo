<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 10:25
 */
namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
class PasswordResetRequestForm extends Model
{
    public $mobile;
    public $smsCode;

    public function rules()
    {
        return [
            ['mobile', 'required','on' => ['default','login_sms_code']],
            ['mobile', 'integer','on' => ['login_sms_code']],
            ['mobile','match','pattern'=>'/^1[0-9]{10}$/','on' => ['default','login_sms_code'],'message'=>'手机号码必须为1开头的11位纯数字'],
            ['mobile', 'string', 'min'=>11,'max' => 11,'on' => ['default','login_sms_code']],
            ['smsCode', 'required','on' => ['default','login_sms_code']],
            ['smsCode', 'integer','on' => ['default','login_sms_code']],
            ['smsCode', 'string', 'min'=>6,'max' => 6,'on' => ['default','login_sms_code']],
            ['smsCode', 'required','requiredValue'=>$this->getSmsCode(),'on' => ['default','login_sms_code'],'message'=>'手机验证码输入错误'],
        ];
    }

    public function getSmsCode(){
        if(!Yii::$app->session->isActive){
            Yii::$app->session->open();
        }
        //取得验证码和短信发送时间session
        $signup_sms_code = intval(Yii::$app->session->get('login_sms_code'));
        $signup_sms_time = Yii::$app->session->get('login_sms_time');
        if(time()-$signup_sms_time < 600){
            return $signup_sms_code;
        }else{
            return 888888;
        }
    }
}