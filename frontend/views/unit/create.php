<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
$this->title = '商品单位创建';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <?php $form=ActiveForm::begin(['action'=>['unit/create'],
        'method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ]);?>
	<h4 class="orders-newtade">单位信息</h4>
    <div class="orders-new clearfix">
        <p>单位国际ID:</p>
        <?php echo $form->field($model,'unit_id')->textInput()->label(false)->hint("<label>*请填写国际商品计量单位ID</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p>单位名称:</p>
        <?php echo $form->field($model,'unit')->textInput()->label(false)->hint("<label>*请输入计量单位名称</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <?php echo $form->field($model,'sort')->textInput(['value'=>'999'])->label(false)->hint("<label>*</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activetextarea($model,'remark',['class'=>'orders-newt2','autofocus'=>false])?><?=Html::error($model,'remark',['class'=>'boxlfts1'])?>
    </div>
    <?=Html::activeInput('hidden',$model,'add_user_id',['value'=>yii::$app->user->identity->id])?>
    <?=Html::activeInput('hidden',$model,'add_user_name',['value'=>yii::$app->user->identity->username])?>
    <?=Html::activeInput('hidden',$model,'create_time',['value'=>time()])?>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' => 'boxlf-but', 'name' => 'login-button']) ?>
        <a href="<?=Url::to(['unit/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
    <?php ActiveForm::end();?>
</div>

