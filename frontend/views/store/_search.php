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

    <input type="text" name="name" placeholder ="请输入入驻商家名称">

    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>

    <?php ActiveForm::end(); ?>
