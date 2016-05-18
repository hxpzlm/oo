<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
$this->title = '品牌创建';
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
    <?php $form=ActiveForm::begin([
        'action'=>['brand/create'],
        'method'=>'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ])?>
	<h4 class="orders-newtade">品牌信息</h4>
    <div class="orders-new clearfix">
        <p>品牌名称:</p>
        <?php echo $form->field($model,'name')->textInput()->label(false)->hint("<label>*请输入品牌名称</label>");?>
    </div>
    <div class="orders-new clearfix">
        <p>状态:</p>
        <div class="aaa">
            <input type="radio" name="Brand[status]" value="1" checked="checked"><label>正常</label>
            <input type="radio" name="Brand[status]" value="0" ><label>停用</label>
        </div>
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
        <a href="<?=Url::to(['brand/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
    <?php ActiveForm::end()?>
</div>
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
        $('#brand-remark').blur(function(){
            var aa=$(this).val();
            if(aa.length>256){
                $('#no_textarea').html('不能超过256个字符');
            }
        });
    })
</script>
<?php \frontend\components\JsBlock::end()?>

