<?php

use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);

/* @var $this yii\web\View */
/* @var $model frontend\models\Shop */

$this->title = '销售平台-添加';
$this->params['breadcrumbs'][] = ['label' => 'Shops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
