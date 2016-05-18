<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\captcha\Captcha;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
	public $verifyCode;
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['username', 'required','message'=>'用户名不能为空'],
            // password is validated by validatePassword()
			['password', 'required','message'=>'密码不能为空'],
            ['password', 'validatePassword','message'=>'用户名或密码错误'],
			['verifyCode', 'required','message'=>'验证码不能为空'],
			['verifyCode', 'captcha','message'=>'验证码错误'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(),0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
	
	public function loginTime($id){
		if($id)
		    return User::addLoginTime($id);
	    else
			return false;
	}
	
}
