<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<div class="close_btn"><input name="unit" placeholder="请输入计量单位"><img src="statics/img/close_icon.jpg" class="img_css"></div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', '<i class="iconfont">&#xe60d;</i>搜索')) ?>
    </div>

    <?php ActiveForm::end(); ?>

