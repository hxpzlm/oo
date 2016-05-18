<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;

$query = new \yii\db\Query();
$this->title = '商品分类-新建';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>

<!--内容-->
<div class="container">
    <?php $form=ActiveForm::begin(['action'=>['category/create'],
        'method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true
    ])?>
	<h4 class="orders-newtade">商品分类信息</h4>
    <div class="orders-new clearfix">
        <p>分类名称:</p>
        <?php echo $form->field($model,'name')->textInput(['autofocus'=>false])->label(false)->hint("<label>*请输入商品分类名称</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p>状态:</p>
        <div class="aaa">
            <input type="radio" name="Category[status]" value="1" checked="checked"><label>正常</label>
            <input type="radio" name="Category[status]" value="0" ><label>停用</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <?php echo $form->field($model,'sort')->textInput(['value'=>'999'])->label(false)->hint("<label>*</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activetextarea($model,'remark',['class'=>'orders-newt2'])?>
    </div>
    <?=Html::activeInput('hidden',$model,'parent_id',['value'=>$parent_id])?>
    <?=Html::activeInput('hidden',$model,'store_id',['value'=>yii::$app->user->identity->store_id])?>
    <?=Html::activeInput('hidden',$model,'store_name',['value'=>yii::$app->user->identity->store_name])?>
    <?=Html::activeInput('hidden',$model,'add_user_id',['value'=>yii::$app->user->identity->id])?>
    <?=Html::activeInput('hidden',$model,'add_user_name',['value'=>yii::$app->user->identity->username])?>
    <?=Html::activeInput('hidden',$model,'create_time',['value'=>time()])?>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' => 'boxlf-but', 'name' => 'login-button']) ?>
        <a href="<?=Url::to(['category/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
    <?php ActiveForm::end()?>
</div>


