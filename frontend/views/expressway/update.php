<?php
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = '品牌管理-修改';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_global/style.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-ed.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <h4 class="orders-edtade">物流公司信息</h4>
    <?php $form=ActiveForm::begin(['action'=>'',
        'method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ])?>
    <div class="orders-ed clearfix">
        <p>物流公司名称:</p>
        <?php echo $form->field($model,'name')->textInput(['class'=>'orders-new','autofocus'=>true])->label(false)->hint("<label>*</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p style="width: 15%">状态:</p>
        <div class="aaa">
            <?php echo $form->field($model,'status')->radioList(['1'=>'正常','0'=>'停用'])->label(false)?>
        </div>
    </div>
    <div class="orders-ed clearfix">
        <p>顺序:</p>
        <?php echo $form->field($model,'sort')->input(['class'=>'orders-new'])->label(false)->hint("<label>*</label>");?>
    </div>
    <div class="orders-ed clearfix">
        <p class="orders-edt1">备注说明:</p>
        <?php echo $form->field($model,'remark')->textarea(['class'=>'orders-edt2'])->label(false)?>
    </div>

    <div class="orders-edbut">
        <?=Html::submitButton(Yii::t('app','保存'))?>
        <a href="<?=Url::to(['expressway/index'])?>">
            <button class="orders-edbut2" type="button">返回</button>
        </a>
    </div>
    <?php ActiveForm::end();?>
</div>
