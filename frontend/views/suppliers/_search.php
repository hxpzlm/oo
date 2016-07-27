<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\SuppliersSearch */
/* @var $form yii\widgets\ActiveForm */

$name = Yii::$app->request->get('SuppliersSearch');
?>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<div class="close_btn"><?= Html::activeInput('text',$model,'name',['placeholder' => '请直接选择或输入选择供应商名称', 'value'=>$name['name']])?><img src="statics/img/close_icon.jpg" class="img_css"></div>

    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>

    <?php ActiveForm::end(); ?>
