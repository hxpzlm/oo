<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class CommonController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','captcha'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;
        $permissionName = $controller.'/'.$action;
        if(in_array($permissionName,$this->ignoreControl())){
            return true;
        }
        if(!\Yii::$app->user->can($permissionName) && Yii::$app->getErrorHandler()->exception === null){
            throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
        }
        return true;
    }
    public function ignoreControl(){
        return array('site/login','site/error','site/logout','site/captcha','reset/index');
    }
}
