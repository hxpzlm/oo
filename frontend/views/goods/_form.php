<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
$user=Yii::$app->user->identity;
$store_id=$user->store_id;
$store_name=$user->store_name;
$user_id=$user->user_id;
$username=$user->username;

/* @var $this yii\web\View */
/* @var $model frontend\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>
<?php if($model->isNewRecord){?>
    <?php $form=ActiveForm::begin(['action'=>['goods/create'],'method'=>'post','enableAjaxValidation' => true,
        'enableClientValidation' => true,])?>
<?php }else{?>
    <?php $form=ActiveForm::begin(['action'=>['goods/update','id'=>$model->goods_id],'method'=>'post','enableAjaxValidation' => true,
        'enableClientValidation' => true,])?>
<?php }?>
<?php echo $form->field($model,'store_id')->hiddenInput(['value'=>$store_id])?>
<?php echo $form->field($model,'store_name')->hiddenInput(['value'=>$store_name])?>
<?php echo $form->field($model,'create_time')->hiddenInput(['value'=>time()])?>
<?php echo $form->field($model,'add_user_id')->hiddenInput(['value'=>$user_id])?>
<?php echo $form->field($model,'add_user_name')->hiddenInput(['value'=>$username])?>
<h4 class="orders-newtade">商品基本信息</h4>
<div class="orders-new clearfix">
    <p>商品中英文名称:</p>
    <?php echo $form->field($model,'name')->textInput()->label(false)->hint("<label>*请输入商品名称</label>");?>
</div>
<div class="orders-new clearfix">
    <p>规格:</p>
    <?php echo $form->field($model,'spec')->textInput()->label(false)->hint("<label>*请输入商品规格</label>");?>
</div>
<div class="orders-new clearfix">
    <p>品牌:</p>
    <?php echo $form->field($model,'brand_id')->DropDownList($brand_row)->label(false)->hint(" <label>*请选择商品品牌</label>")?>
</div>
<div class="orders-new clearfix">
    <p>单位:</p>
    <?php echo $form->field($model,'unit_name')->textInput()->label(false)->hint("<label>*请输入商品单位（如：盒、个、瓶、罐等）</label>");?>
</div>
<div class="orders-new clearfix">
    <p>条形码:</p>
    <?php echo $form->field($model,'barode_code')->textInput()->label(false)->hint("<label>*请输入商品包装上的条形码数字</label>");?>
</div>
<div class="orders-new clearfix">
    <p>净重:</p>
    <?php echo $form->field($model,'weight')->textInput()->label(false)->hint("<label>*kg</label>");?>
</div>
<div class="orders-new clearfix">
    <p>体积:</p>
    <?php echo $form->field($model,'volume')->textInput()->label(false)->hint("<label>*m<sup>3</sup></label>");?>

</div>
<div class="orders-new clearfix">
    <p>保质期:</p>
    <?php echo $form->field($model,'shelf_life')->textInput()->label(false)->hint("<label>*天</label>");?>

</div>
<div class="orders-new clearfix">
    <p>主要成分:</p>
    <?php echo $form->field($model,'element')->textInput()->label(false)?>
</div>
<div class="orders-new clearfix">
    <p>功效:</p>
    <?php echo $form->field($model,'virtue')->textInput()->label(false)?>
</div>
<div class="orders-new clearfix">
    <p>适用人群:</p>
    <?php echo $form->field($model,'painter')->textInput()->label(false)?>
</div>
<div class="orders-new clearfix">
    <p>服用方法:</p>
    <?php echo $form->field($model,'suggest')->textInput()->label(false)?>
</div>
<div class="orders-new clearfix">
    <p>储存方法:</p>
    <?php echo $form->field($model,'store_mode')->textInput()->label(false)?>
</div>
<div class="orders-new clearfix">
    <p class="orders-newt1">介绍:</p>
    <?= Html::activetextarea($model,'intro',['class'=>'orders-newt2'])?>

</div>
<h4 class="orders-newtade">其他信息</h4>
<div class="orders-new clearfix">
    <p>所属分类:</p>
    <?php echo $form->field($model,'cat_id')->DropDownList($cat_row)->label(false)->hint("<label>*请选择商品分类</label>");?>
</div>
<div class="orders-new clearfix">
    <p>负责人:</p>
    <?php echo $form->field($model,'principal_id')->DropDownList($principal_row)->label(false)->hint("<label>*</label>");?>
</div>
<div class="orders-new clearfix">
    <p>顺序:</p>
    <?php echo $form->field($model,'sort')->textInput()->label(false)->hint("<label>*</label>");?>

</div>
<div class="orders-newbut">
    <?= Html::submitButton('保存') ?>
    <a href="<?=Url::to(['goods/index'])?>">
        <button class="orders-newbut2" type="button">返回</button>
    </a>
</div>
<?php ActiveForm::end();?>

