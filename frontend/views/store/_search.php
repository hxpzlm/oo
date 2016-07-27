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

<div class="close_btn"><input type="text" name="name" placeholder ="请输入入驻商家名称"><img src="statics/img/close_icon.jpg" class="img_css"></div>

    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>

    <?php ActiveForm::end(); ?>
