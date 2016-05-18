<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <input type="text" name="name" placeholder ="请输入品牌名称">
    <div class="form-group">

        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
