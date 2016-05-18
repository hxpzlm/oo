<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
$this->title = '仓库创建';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_global/style.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <?php $form=ActiveForm::begin(['action'=>['warehouse/create'],
        'method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ])?>
	<h4 class="orders-newtade">仓库信息</h4>
    <div class="orders-new clearfix">
        <p>仓库名称:</p>
        <?php echo $form->field($model,'name')->textInput()->label(false)->hint("<label>*请输入唯一仓库名称</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p>状态:</p>
        <div class="aaa">
            <input type="radio" value="1" name="WarehouseModel[status]" checked="checked"><label>正常</label>
            <input type="radio" value="0" name="WarehouseModel[status]"><label>停用</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>负责人:</p>
        <?php echo $form->field($model,'principal_id')->DropDownList($principal_row)->label(false)->hint("<label>*</label>")?>
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <?php echo $form->field($model,'sort')->textInput(['value'=>'999'])->label(false)->hint("<label>*</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activetextarea($model,'remark',['class'=>'orders-newt2','autofocus'=>false])?>
    </div>
    <?=Html::activeInput('hidden',$model,'store_id',['value'=>yii::$app->user->identity->store_id])?>
    <?=Html::activeInput('hidden',$model,'store_name',['value'=>yii::$app->user->identity->store_name])?>
    <?=Html::activeInput('hidden',$model,'add_user_id',['value'=>yii::$app->user->identity->id])?>
    <?=Html::activeInput('hidden',$model,'add_user_name',['value'=>yii::$app->user->identity->username])?>
    <?=Html::activeInput('hidden',$model,'create_time',['value'=>time()])?>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' => 'boxlf-but', 'name' => 'login-button']) ?>
        <a href="<?=Url::to(['warehouse/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
    <?php ActiveForm::end();?>
</div>


