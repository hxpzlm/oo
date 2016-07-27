<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Check */

$this->title = '库存盘点-编辑 ' . $model->check_id;
$this->params['breadcrumbs'][] = ['label' => 'Checks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->check_id, 'url' => ['view', 'id' => $model->check_id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="main">
    <?= $this->render('_form', [
        'model' => $model,
        'cg_model'  => $cg_model,
    ]) ?>
</div>
