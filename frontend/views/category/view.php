<?php
use frontend\assets\AppAsset;
use yii\helpers\Url;
$this->title = '商品分类-查看';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>


<!--内容-->
<div class="container">
	<h4 class="orders-newtade">商品分类信息</h4>
	<div class="orders-look clearfix">
        <p class="orders-lookt1">分类名称:</p>
		<p class="orders-lookt2"><?echo $model->name?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">状态:</p>
		<p class="orders-lookt2"><?php if($model->status==0){echo '停用';}else{echo '正常';}?></p>
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
		<a href="<?=Url::to(['category/index'])?>">
			<button class="orders-lookut" type="button">返回</button>
		</a>
	</div>
</div>


