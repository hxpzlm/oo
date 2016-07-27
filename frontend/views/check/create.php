<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Check */

$this->title = '库存盘点-新增';
$this->params['breadcrumbs'][] = ['label' => 'Checks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="main">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
