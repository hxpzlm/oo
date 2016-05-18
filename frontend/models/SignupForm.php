<?php
namespace frontend\models;

use common\models\User;
use Yii;

/**
 * Signup form
 */
class SignupForm extends \yii\db\ActiveRecord
{

    public $username;
    public $sex;
    public $store_id;
    public $sort;
    public $real_name;
    public $email;
    public $mobile;
    public $status;
    public $type;

	
	
	public static function tableName()
    {
        return '{{%user}}';
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required','message'=>"用户名不能为空"],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '该用户名已经存在'],
            ['username', 'string', 'min' => 6, 'max' => 20,'tooLong'=>'用户名长度不能超过个字符','tooShort'=>'用户名长度不能少于6个字符'],
			['username','match','pattern'=>'/^[a-zA-Z][a-zA-Z0-9_]*$/','message'=>'账号必须为英文开头的字符串'],
            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile', 'required','message'=>'手机号码不能为空'],
            ['mobile', 'integer'],
            ['mobile','match','pattern'=>'/^1[0-9]{10}$/','message'=>'电话必须为1开头的11位纯数字'],
			
            ['mobile', 'unique', 'targetClass' => '\common\models\User', 'message' => '该电话号码已经存在'],
			
            [['sex','type','status','sort'],'safe'],
            ['store_id','integer'],
			
            ['real_name','required','message'=>'姓名不能为空'],
            ['real_name','string','min' => 2,'max'=>20,'tooLong'=>'姓名长度不能超过个字符','tooShort'=>'姓名长度不能少于2个字符'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'mobile' => '手机号码',
            'real_name' => '姓名'
        ];
    }
}
