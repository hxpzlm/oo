<?php
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-ed.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
$this->title = '入驻商家-修改';
$this->params['breadcrumbs'][] = $this->title;
?>

<!--内容-->
<div class="container">
	<h4 class="orders-edtade">入驻商家信息</h4>
    <?php $form=ActiveForm::begin(['action' => '','method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,])?>
	<div class="orders-ed clearfix">
		<p>入驻商家名称:</p>
		<?php echo $form->field($model,'name')->textInput(['class'=>'orders-new','autofocus'=>true])->label(false)->hint("<label>*</label>");?>
	</div>
    <div class="orders-new clearfix"  >
        <p style="width: 15%">客户类型:</p>
        <div class="aaa" id="customers-type">
            <?php echo $form->field($model,'status')->radioList(['1'=>'正常','0'=>'停用'])?>
        </div>
    </div>
	<div class="orders-ed clearfix">
		<p>顺序:</p>
		<?php echo $form->field($model,'sort')->textInput(['class'=>'orders-new'])->label(false)->hint("<label>*</label>");?>
	</div>
	<div class="orders-ed clearfix">
		<p>备注说明:</p>
        <?php echo $form->field($model,'remark')->textarea(['class'=>'orders-edt2'])?>
	</div>
    <div class="orders-newbut">
        <?= Html::submitButton(Yii::t('app', '保存')) ?>
        <a href="<?=Url::to(['store/index'])?>">
            <span class="orders-newbut2">返回</span>
        </a>
    </div>
    <?php ActiveForm::end()?>
</div>
