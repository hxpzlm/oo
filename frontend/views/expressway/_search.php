<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Expressway */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expressway-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="close_btn"><input type="text" name="name" placeholder ="请输入物流公司名称"><img src="statics/img/close_icon.jpg" class="img_css"></div>

    <div class="form-group">
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
