<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</div>
<!--导航-->

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
