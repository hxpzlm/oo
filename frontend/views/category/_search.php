<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\CategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <input type="text" name="name">

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', '<i class="iconfont">&#xe60d;</i>搜索')) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
