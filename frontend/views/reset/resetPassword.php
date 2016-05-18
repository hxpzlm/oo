<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
$this->registerCssFile('@web/statics/css/modifyPassword.css',['depends'=>['yii\web\YiiAsset']]);
$this->title = '密码重置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modifyPassword_container">
    <div class="modifyPassword_main">
        <div class="modifyPassword_main_top"><?= Html::encode($this->title) ?></div>
        <?=Html::beginForm('','post',['id'=>'modifyPassword02'])?>
        <p id="info"></p>
        <p>
            <label>新的登录密码：</label>
            <?=Html::activeInput('password',$model,'password',['autofocus' => true,'id'=>'modifyPassword01_password','placeholder'=>'请输入密码'])?>
        </p>
        <p>
            <label>确认登录密码：</label>
            <?=Html::activeInput('password',$model,'repassword',['id'=>'modifyPassword01_password','placeholder'=>'请再次输入密码'])?>
        </p>
        <p class="">
            <?= Html::submitButton('确定', ['class' => 'modifyPassword_next']) ?>
        </p>
        <?=Html::endForm();?>
    </div>
</div>
