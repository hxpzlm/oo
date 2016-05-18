<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Suppliers */
/* @var $form yii\widgets\ActiveForm */

$user = new User();
$userinfo = $user->findIdentity(Yii::$app->session['__id']);
?>
<?php $form = ActiveForm::begin(); ?>
<?php if(!empty($model->isNewRecord)){?>
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['username']])?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家id-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?>
<?php }?>

    <h4 class="orders-newtade">销售平台信息</h4>
    <div class="orders-new clearfix">
        <p>平台名称:</p>
        <?= $form->field($model, 'name')->textInput()->label(false)->hint('<label>* 请输入销售平台名称，不允许存在相同名称的销售平台。</label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>状态:</p>
        <div class="aaa">
            <?php if(!empty($model->isNewRecord)){?>
                <input type="radio" name="Shop[status]" value="1" checked="checked"><label>正常</label>
                <input type="radio" name="Shop[status]" value="0" ><label>停用</label>
            <?php }else{?>
                <?= Html::activeRadioList($model,'status',[1=>'正常',0=>'停用'])?>
            <?php }?>
        </div>
        <span>停用后在其他功能中将不能选择到该销售平台。</span>
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <?= $form->field($model, 'sort')->textInput(['value'=>999])->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>备注说明:</p>
        <?= Html::activeInput('text',$model,'remark')?>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton( '保存',  ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['shop/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
<?php ActiveForm::end(); ?>
