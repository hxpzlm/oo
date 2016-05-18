<?php
use frontend\assets\AppAsset;
use yii\helpers\Url;
$this->title = '商品单位-查看';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_global/style.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>商品单位-查看</title>
<link type="text/css" href="statics/css/css_global/style.css" rel="stylesheet" />
<link rel="stylesheet" href="statics/svg/iconfont.css" />
<link type="text/css" href="statics/css/purchaseOrders-look.css" rel="stylesheet" />
<script type="text/javascript" src="statics/js/js_global/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="statics/js/js_global/global.js"></script>
</head>
<body>

<!--内容-->
<div class="container">
	<h4 class="orders-newtade">商品单位信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">商品单位ID:</p>
		<p class="orders-lookt2"><?echo $model->unit_id?></p>
	</div>
	<div class="orders-look clearfix">
        <p class="orders-lookt1">商品单位名称:</p>
		<p class="orders-lookt2"><?echo $model->unit?></p>
	</div>
	<div class="orders-look clearfix">
        <p class="orders-lookt1">顺序:</p>
        <p class="orders-lookt2"><?php echo $model->sort?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt3">备注说明:</p>
		<p class="orders-lookt4"><?php echo $model->remark?></p>
	</div>
    <h4 class="orders-newtade">系统信息</h4>

    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建人</p>
        <p class="orders-lookt2"><?php echo $model->add_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建时间:</p>
        <p class="orders-lookt2"><?php echo Yii::$app->formatter->asDate($model->create_time, 'php:Y-m-d')?></p>
    </div>
	<div class="orders-lookbut">
		<a href="<?=Url::to(['unit/index'])?>">
			<button class="orders-lookut" type="button">返回</button>
		</a>
	</div>
</div>


