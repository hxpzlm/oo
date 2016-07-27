<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->registerCssFile('@web/statics/css/modifyPassword.css',['depends'=>['yii\web\YiiAsset']]);
$this->title = '找回密码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modifyPassword00_top">
    <div>
        <?= Html::encode($this->title) ?>
    </div>
</div>

<div class="modifyPassword_container">
    <div class="modifyPassword_main">
        <div class="modifyPassword_main_top">输入账号</div>
        <?=Html::beginForm('','post',['id'=>'modifyPassword00']);?>
            <p>
                <label>账&nbsp;&nbsp;&nbsp;号：</label>
                <?=Html::activeInput('text',$model,'username',['id'=>'modifyPassword00_username','placeholder'=>'请输入账号']);?>
                <?=Html::error($model,'username',['class'=>'boxlfts1'])?>
            </p>
            <p>
                <label>验证码：</label>
                <?=yii\captcha\Captcha::widget([
                    'model' => $model,
                    'attribute'=>'verify',
                    'captchaAction'=>'reset/captcha',
                    'template'=>"{input}<div class='verificationCode'><div>{image}</div></div>",
                    'options'=>[
                        'placeholder'=> "请输入下方验证码",
                        'id'=>'verificationCode',
                    ],'imageOptions' => [
                        'alt' => '点击更换验证码',
                    ]
                ]);?>
                <?=Html::error($model,'verify',['class'=>'boxlfts3'])?>
            </p>
            <p class="">
                <?= Html::submitButton('下一步', ['class' => 'modifyPassword_next', 'name' => 'login-button']) ?>
                <a href="<?=\yii\helpers\Url::to(['site/login'])?>">取消</a>
            </p>
        <?=Html::endForm();?>
    </div>
</div>
