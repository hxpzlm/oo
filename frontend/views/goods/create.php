<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->title = '商品-添加';
$this->params['breadcrumbs'][] = ['label' => 'goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<!--内容-->
<div class="container">
    <?= $this->render('_form', [
        'model' => $model,
        'brand_row' => $brand_row,
        'cat_row' => $cat_row,
        'principal_row' => $principal_row,
    ]) ?>
</div>