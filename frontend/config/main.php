<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
			'autoRenewCookie' => true,
			'authTimeout' => 3600,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 's2_auth_item',
            'assignmentTable' => 's2_auth_assignment',
            'itemChildTable' => 's2_auth_item_child',
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'smser' => [
            // 中国互亿无线
            'class' => 'frontend\components\HuyiSms',
            'username' => 'cf_xieryaoye',
            'password' => 'xixieryaoye!!',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
