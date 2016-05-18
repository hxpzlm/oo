<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\SuppliersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<?= Html::activeInput('text',$model,'username',['placeholder' => '请直接选择或输入选择客户账号'])?>

<?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>

<?php ActiveForm::end(); ?>