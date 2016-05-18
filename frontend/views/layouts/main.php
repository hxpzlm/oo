<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use frontend\components\menuHelper;
AppAsset::register($this);
$this->registerJsFile('@web/statics/js/js_plug/jquery.cookie.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("$(function(){
     if($('.navboxs-list li').hasClass('listinx')){
			$('.listinx').parents('.navboxs').addClass('shows');
			$('.listinx').parents('li').addClass('inx');
		}
});",\yii\web\View::POS_END);
$arr= [
	['title'=>'采购','url'=>'purchase/index','parent'=>5],
	['title'=>'销售','url'=>'order/index','parent'=>3],
	['title'=>'库存','url'=>'stocks/index','parent'=>4],
    ['title'=>'统计','url'=>'order/count','parent'=>6],
	['title'=>'商品','url'=>'goods/index','parent'=>2],
	['title'=>'系统设置','url'=>'user/index','parent'=>1]
];
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="save" content="history">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="head">
	<div class="heads clearfix">
		<img src="statics/img/logo.jpg">
		<h1>供应链管理系统</h1>
	</div>
	<ul class="headrg clearfix">
		<li>欢迎您，<?=Yii::$app->user->identity->username;?></li>
		<li>|</li>
		<li class="headrg-exit"><a href="<?=Url::to(['user/profile'])?>">修改资料</a></li>
		<li>|</li>
		<li class="headrg-exit"><a href="<?=Url::to(['site/logout'])?>" data-method="post">退出登录</a></li>
	</ul>
</div>
<!--导航-->

<ul class="nav">
	<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'site/index')){?>
	<li <?php if(Yii::$app->controller->id == 'site'){ echo 'class="inx"';}?>><a class="nav-a" href="<?=Url::to(['site/index'])?>">首页</a></li>
	<?php }?>

	<?php
	    foreach($arr as $v){
			$menu = menuHelper::getAssignmentMenu($v['parent']);
			if($menu){
				if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,$v['url'])){
	?>
		  <li class="one_md"><a class="nav-a" href="<?=Url::to([$v['url']])?>"><?=$v['title']?></a>

			  <div class="navboxs">
				  <ul class="navboxs-list">
					  <?php foreach($menu as $val){
						  if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,$val['name'])){?>
						  <a href="<?=Url::to([$val['name']])?>"><li <?php if(Yii::$app->controller->id.'/'.Yii::$app->controller->action->id==$val['name']){echo 'class="listinx"';}?>><?=$val['menu_name']?></li></a>
					  <?php
						  }
					  }?>
				  </ul>
			  </div>
		  </li>
	<?php
				}
			}
		}?>


</ul>
<!--头部 end-->
<?=Alert::widget()?>
<?= $content ?>
<!--底部-->
<div class="bot-box">
	<div class="bot-boxll1">
		<i class="iconfont">&#xe600;</i>
		<i class="iconfont">&#xe609;</i>
	</div>
	<ul class="bot-boxl2">
		<li>关于我们</li>
		<li>|</li>
		<li>服务条款</li>
		<li>|</li>
		<li>隐私协议</li>
		<li>|</li>
		<li>更新日志</li>
	</ul>
	<p class="bot-boxl3">©2015维他购公司 湘ICP证XXXXXX号</p>
</div>
<!--底部 end-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
