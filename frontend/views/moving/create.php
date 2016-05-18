<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
		laydate({elem: '#ex-date'});//绑定元素
		laydate({elem: '#pur-date'});
	}();", \yii\web\View::POS_END);

$this->title = '库存调剂-新增';
$this->params['breadcrumbs'][] = ['label' => 'moving', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<!--内容-->
<div class="container">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
