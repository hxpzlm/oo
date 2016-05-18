<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;

$u = new User();
$userinfo = $u->findIdentity(Yii::$app->session['__id']);

/* @var $this yii\web\View */
/* @var $model frontend\models\Suppliers */
/* @var $form yii\widgets\ActiveForm */

$user = \frontend\components\Search::SearchUser();
$user_row = array();
if(!empty($user)){
    $user_row[''] = '请选择';
    foreach($user as $value){
        if($value['type']==2){
            $user_row[$value['user_id']] = $value['username'];
        }
    }
}

$country_row = array('' => '请选择','中国大陆' => '中国大陆','香港' => '香港','澳门' => '澳门','台湾' => '台湾','韩国' => '韩国','日本' => '日本','美国' => '美国','加拿大' => '加拿大','英国' => '英国','新加坡' => '新加坡','马来西亚' => '马来西亚','泰国' => '泰国','越南' => '越南','菲律宾' => '菲律宾','印度尼西亚' => '印度尼西亚','意大利' => '意大利','俄罗斯' => '俄罗斯','新西兰' => '新西兰','荷兰' => '荷兰','瑞典' => '瑞典','澳大利亚' => '澳大利亚','乌克兰' => '乌克兰','法国' => '法国','德国' => '德国','其他' => '其他');
?>
<?php $form = ActiveForm::begin(); ?>
<?php if(!empty($model->isNewRecord)){?>
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['username']])?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家id-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?>
<?php }?>
<h4 class="orders-newtade">供应商基本信息</h4>
<div class="orders-new clearfix">
    <p>供应商名称:</p>
    <?= $form->field($model, 'name')->textInput()->label(false)->hint('<label>* 请输入供应商名称，不允许同一入驻商家下存在相同名称的供应商。</label>') ?>
</div>
<div class="orders-new clearfix">
    <p>国别:</p>
    <?= $form->field($model, 'country')->dropDownList($country_row)->label(false)->hint('<label>* 请选择供应商所属的国家。</label>') ?>
</div>
<div class="orders-new clearfix">
    <p>城市名:</p>
    <?= Html::activeInput('text',$model,'city')?>
    <span>请输入供应商所在国家的城市名称。</span>
</div>
<div class="orders-new clearfix">
    <p>联系人:</p>
    <?= $form->field($model, 'contact_man')->textInput()->label(false)->hint('<label>* </label>') ?>
</div>
<div class="orders-new clearfix">
    <p>电话:</p>
    <?= $form->field($model, 'mobile')->textInput()->label(false)->hint('<label>* </label>') ?>
</div>
<div class="orders-new clearfix">
    <p>传真:</p>
    <?= Html::activeInput('text',$model,'fax')?>
</div>
<div class="orders-new clearfix">
    <p>邮箱:</p>
    <?= $form->field($model, 'email')->textInput()->label(false)->hint('<label>* </label>') ?>
</div>
<div class="orders-new clearfix">
    <p>地址:</p>
    <?= Html::activeInput('text',$model,'address')?>
</div>
<h4 class="orders-newtade">其他信息</h4>
<div class="orders-new clearfix">
    <p>负责人:</p>
    <?= $form->field($model, 'shop_manage_principal')->dropDownList($user_row)->label(false)->hint('<label>* 可选值为系统中的普通管理员姓名。</label>') ?>
</div>
<div class="orders-new clearfix">
    <p>状态:</p>
    <div class="aaa">
        <?php if(!empty($model->isNewRecord)){?>
            <input type="radio" name="Suppliers[status]" value="1" checked="checked"><label>正常</label>
            <input type="radio" name="Suppliers[status]" value="0" ><label>停用</label>
        <?php }else{?>
            <?= Html::activeRadioList($model,'status',[1=>'正常',0=>'停用'])?>
        <?php }?>
    </div>
    <span>停用后采购时将不能选择到该供应商。</span>
</div>
<div class="orders-new clearfix">
    <p>顺序:</p>
    <?= $form->field($model, 'sort')->textInput(['value'=>999])->label(false)->hint('<label>* </label>') ?>
</div>
<div class="orders-new clearfix">
    <p class="orders-newt1">备注说明:</p>
    <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
</div>
<div class="orders-newbut">
    <?= Html::submitButton( '保存',  ['class' =>'orders-edbut']) ?>
    <a href="<?=Url::to(['suppliers/index'])?>">
        <button class="orders-newbut2" type="button">返回</button>
    </a>
</div>
<?php ActiveForm::end(); ?>
