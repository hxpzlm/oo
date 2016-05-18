<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\RefuseModel */

$this->title = $model->refuse_id;
$this->params['breadcrumbs'][] = ['label' => 'Refuse Models', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="refuse-model-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->refuse_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->refuse_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refuse_id',
            'order_no',
            'order_id',
            'store_id',
            'store_name',
            'warehouse_id',
            'warehouse_name',
            'shop_id',
            'shop_name',
            'sale_time:datetime',
            'refuse_amount',
            'reason:ntext',
            'confirm_time:datetime',
            'create_time:datetime',
            'add_user_id',
            'add_user_name',
            'customer_id',
            'customer_name',
            'confirm_user_id',
            'confirm_user_name',
            'remark:ntext',
            'refuse_time:datetime',
            'status',
            'refuse_real_pay',
        ],
    ]) ?>

</div>
