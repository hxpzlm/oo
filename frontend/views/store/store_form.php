<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Store */
/* @var $form ActiveForm */
?>
<div class="store_form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sort') ?>
        <?= $form->field($model, 'status') ?>
        <?= $form->field($model, 'add_user_id') ?>
        <?= $form->field($model, 'create_time') ?>
        <?= $form->field($model, 'remark') ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'add_user_name') ?>
    
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- store_form -->
