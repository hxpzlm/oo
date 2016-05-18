<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);

$this->title = '供应商-添加';
$this->params['breadcrumbs'][] = ['label' => 'Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--内容-->
<div class="container">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>