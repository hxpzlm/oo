<?php
namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\captcha\Captcha;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $username;
    public $verify;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username','required','message'=>'用户名不能为空'],
            ['username', 'filter', 'filter' => 'trim'],
            //['mobile', 'match','pattern'=>'/^1[0-9]{10}$/','message'=>'{attribute}必须为1开头的11位纯数字'],
            ['username', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => '没有查询到相匹配的用户'
            ],
            ['verify', 'captcha', 'captchaAction' =>'reset/captcha','message'=>'验证码错误'],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendUserName()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'username' => $this->username,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }
        
        if (!$user->save()) {
            return false;
        }

//        return Yii::$app
//            ->mailer
//            ->compose(
//                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
//                ['user' => $user]
//            )
//            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
//            ->setTo($this->email)
//            ->setSubject('Password reset for ' . \Yii::$app->name)
//            ->send();
        return true;
    }

    public function getMobile($name){
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'username' => $name,
        ]);

        if (!$user) {
            return false;
        }
        return $user->mobile;
    }
}
