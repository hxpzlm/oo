<?php
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = '商品单位-修改';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_global/style.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-ed.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
	<h4 class="orders-edtade">计量单位信息</h4>
    <?php $form=ActiveForm::begin(['action'=>'',
        'method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ])?>
    <div class="orders-ed clearfix">
		<p>计量单位ID:</p>
		<?php echo $form->field($model,'unit_id')->textInput(['class'=>'orders-new','autofocus'=>true])->label(false)->hint("<label>*</label>");?>
	</div>
	<div class="orders-ed clearfix">
		<p>计量单位名称:</p>
		<?php echo $form->field($model,'unit')->textInput(['class'=>'orders-new'])->label(false)->hint("<label>*</label>");?>
	</div>
	<div class="orders-ed clearfix">
		<p>顺序:</p>
        <?php echo $form->field($model,'sort')->input(['class'=>'orders-new'])->label(false)->hint("<label>*</label>");?>
	</div>
	<div class="orders-ed clearfix">
		<p class="orders-edt1">备注说明:</p>
        <?php echo $form->field($model,'remark')->textarea(['class'=>'orders-edt2'])?>
	</div>

	<div class="orders-newbut">
		<?=Html::submitButton(Yii::t('app','保存'))?>
		<a href="<?=Url::to(['unit/index'])?>">
			<span class="orders-newbut2">返回</span>
		</a>
	</div>
    <?php ActiveForm::end();?>
</div>


