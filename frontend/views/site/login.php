<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$this->title = '登录-供应链管理系统';
$this->params['breadcrumbs'][] = $this->title;
?>
<link type="text/css" href="statics/css/logon.css" rel="stylesheet" />

<div class="logon-box clearfix">
	<div class="logon-boxlf">
		<p class="logon-boxlft1">登录供应链管理系统</p>

		<?php $form = ActiveForm::begin([
			'id' => 'login-form',
			'fieldConfig' => [
				'template' => "{input}<div class=\"col-lg-8\">{hint}</div>{error}",
			],
		]); ?>
		<?= $form->field($model, 'username')->label(false)->hint('　')->textInput(['class'=>'name','placeholder'=>'请输入账号','autofocus'=>true]) ?>

		<?= $form->field($model, 'password')->passwordInput(['class'=>'pswd','placeholder'=>'请输入密码'])->hint('　',['id'=>'rt'])->label(false) ?>


		<?= $form->field($model, 'verifyCode')->widget(yii\captcha\Captcha::className(), [
			'template' => '{input}<span id="img">{image}</span>',
			'options'=>[
				'placeholder'=> "请输入右侧验证码",
			],'imageOptions' => [
				'alt' => '点击更换验证码',
			]
		])->label(false)->hint("　")?>

		 <?= Html::submitButton('登录', ['class' => 'boxlf-but', 'name' => 'login-button']) ?>
		<?php ActiveForm::end(); ?>
		<div class="boxlf-wjmm"><?= Html::a('忘记密码', ['reset/index'],['data-method'=>'post']) ?></div>
	</div>
	<div class="logon-boxrg">
		<img src="statics/img/maimg.jpg">
		<p>关注我有惊喜！</p>
	</div>
</div>
<?php \frontend\components\JsBlock::begin()?>
    <script>
	    $(function(){
			if($('input[type="password"]').parent().hasClass('has-error')){
				
				$('#rt').text('用户名或密码错误');
			}
		})
    </script>
<?php \frontend\components\JsBlock::end()?>
