<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Goods */
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);

$this->title = '商品修改';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Goods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->goods_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="goods-update">

    <?= $this->render('_form', [
        'model' => $model,
        'brand_row'=>$brand_row,
        'cat_row' => $cat_row,
        'principal_row' => $principal_row,
    ]) ?>

</div>
