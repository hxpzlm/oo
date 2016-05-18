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
if(!empty($userinfo['store_id'])){
    $s_store_id = ' and store_id='.$userinfo['store_id'];
}else{
    $s_store_id = '';
}

$query = new \yii\db\Query();
//获取客户来源
$shopList = $query->select('shop_id,name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('status=1'.$s_store_id)->all();
$shop_row = array();
if(!empty($shopList)){
    $shop_row[''] = '请选择';
    foreach($shopList as $value){
        $shop_row[$value['shop_id']] = $value['name'];
    }
}

$this->registerJs("
//创建新的收货人
window.onload = function(){
    var btnBox = document.getElementById('goodsReceipt');
    var btn = document.getElementById('goodsReceipt_btn');
    btn.onclick = function(){
        var oDiv = document.createElement('div');
        var oInput = document.createElement('input');
        oInput.name = 'Address[accept_name][]';
        oInput.placeholder = '姓名*';
        oInput.className = 'goodsReceipt01';
        oDiv.appendChild(oInput);

        var oInput = document.createElement('input');
        oInput.name = 'Address[accept_mobile][]';
        oInput.placeholder = '电话*';
        oInput.className = 'goodsReceipt02';
        oDiv.appendChild(oInput);

        var oInput = document.createElement('input');
        oInput.name = 'Address[accept_address][]';
        oInput.placeholder = '收货地址*';
        oInput.style.width = 280 + 'px';
        oDiv.appendChild(oInput);

        var oInput = document.createElement('input');
        oInput.name = 'Address[zcode][]';
        oInput.placeholder = '邮政编码*';
        oInput.style.width = 100 + 'px';
        oDiv.appendChild(oInput);

        var oInput = document.createElement('input');
        oInput.name = 'Address[accept_idcard][]';
        oInput.placeholder = '身份证号码';
        oInput.className = 'goodsReceipt04';
        oDiv.appendChild(oInput);

        var oInput = document.createElement('input');
        oInput.name = 'Address[is_idcard][]';
        oInput.type = 'checkbox';
        oInput.className = 'goodsReceipt05';
        oDiv.appendChild(oInput);

        var oSpan = document.createElement('span');
        oInput.value = 1;
        oSpan.innerHTML = '已上传身份证';
        oDiv.appendChild(oSpan);

        var oLabel = document.createElement('label');
        oLabel.innerHTML = '-';
        oLabel.className = 'goodsReceipt_label';
        oLabel.style.marginLeft = 14 + 'px';
        oDiv.appendChild(oLabel);
        btnBox.appendChild(oDiv);
//删除收货人
        oDiv.getElementsByTagName('label')[0].onclick = function(){
            btnBox.removeChild(this.parentNode);
        }
};
}
", \yii\web\View::POS_END);
?>
<?php $form = ActiveForm::begin(); ?>
    <?php if(!empty($model->isNewRecord)){?>
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['username']])?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家id-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?>
    <?php }?>

    <h4 class="orders-newtade">客户基本信息</h4>
    <div class="orders-new clearfix">
        <p>客户来源:</p>
        <?= $form->field($model, 'shop_id')->dropDownList($shop_row,['onchange'=>"$('#customers-shop_name').val($('#customers-shop_id option:selected').text())"])->label(false)->hint('<label>* 请选择客户ID所注册的销售平台。</label>') ?>
        <input type="hidden" id="customers-shop_name" name="Customers[shop_name]" />
    </div>
    <div class="orders-new clearfix">
        <p>客户帐号:</p>
        <?=$form->field($model, 'username')->textInput()->label(false)->hint('<label>* 请输入客户在销售平台中注册的ID，不能存在相同客户来源的ID。</label>')?>
    </div>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <?=$form->field($model, 'real_name')->textInput()->label(false)->hint('<label>* 请输入客户的真实姓名。</label>')?>
    </div>
    <div class="orders-new clearfix">
        <p>性别:</p>
        <div class="aaa">
            <input type="radio" id="customers-sex" name="Customers[sex]" value="1" <?php if($model->sex==1){echo 'checked';}?>><label>男</label>
            <input type="radio" id="customers-sex" name="Customers[sex]" value="0" <?php if($model->sex==0){echo 'checked';}?>><label>女</label>
            <input type="radio" id="customers-sex" name="Customers[sex]" value="2" <?php if($model->sex==2){echo 'checked';}?>><label>保密</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>联系电话:</p>
        <?=$form->field($model, 'mobile')->textInput()->label(false)->hint('<label>*</label>')?>
    </div>
    <div class="orders-new clearfix">
        <p>Email/QQ/其他:</p>
        <?= Html::activeInput('text',$model,'other')?>
    </div>
    <div class="orders-new clearfix">
        <p>地址:</p>
        <?= Html::activeInput('text',$model,'address')?>
    </div>
    <div class="orders-new clearfix">
        <p>客户类型:</p>
        <div class="aaa">
            <input type="radio" id="customers-type" name="Customers[type]" value="0" <?php if($model->type==0){echo 'checked';}?>/><label>个人客户</label>
            <input type="radio" id="customers-type" name="Customers[type]" value="1" <?php if($model->type==1){echo 'checked';}?>/><label>企业客户</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <?=$form->field($model, 'sort')->textInput(['value'=>999])->label(false)->hint('<label>*</label>')?>
    </div>
    <div class="orders-new clearfix">
        <p>备注说明:</p>
        <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-new clearfix">
        <p>收货人信息:</p>
        <div class="goodsReceipt" id="goodsReceipt">
<?php if(empty($model->isNewRecord)){?>
        <?php foreach($address as $row){?>
        <input type="hidden" name="Address[address_id][]" value="<?=$row['address_id']?>">
        <input type="text" name="Address[accept_name][]" value="<?=$row['accept_name']?>" placeholder="姓名*" class="goodsReceipt01">
        <input type="text" name="Address[accept_mobile][]" value="<?=$row['accept_mobile']?>" placeholder="电话*" class="goodsReceipt02">
        <input type="text" name="Address[accept_address][]" value="<?=$row['accept_address']?>" placeholder="收货地址*" style="width: 280px">
        <input type="text" name="Address[zcode][]" value="<?=$row['zcode']?>" placeholder="邮政编码*" style="width: 100px;">
        <input type="text" name="Address[accept_idcard][]" value="<?=$row['accept_idcard']?>" placeholder="身份证号码" class="goodsReceipt04">
        <input type="checkbox" name="Address[is_idcard][]" <?php if($row['is_idcard']==1){echo 'checked';}?> value="<?=$row['is_idcard']?>" class="goodsReceipt05" />
        <span>已上传身份证</span>
        <label id="goodsReceipt_btn" class="goodsReceipt_label">+</label>
        <?php }?>
<?php }else{?>
        <div>
            <input type="text" name="Address[accept_name][]" value="" placeholder="姓名*" class="goodsReceipt01">
            <input type="text" name="Address[accept_mobile][]" value="" placeholder="电话*" class="goodsReceipt02">
            <input type="text" name="Address[accept_address][]" value="" placeholder="收货地址*" style="width: 280px">
            <input type="text" name="Address[zcode][]" value="" placeholder="邮政编码*" style="width: 100px;">
            <input type="text" name="Address[accept_idcard][]" value="" placeholder="身份证号码" class="goodsReceipt04">
            <input type="checkbox" name="Address[is_idcard][]" value="1" class="goodsReceipt05">
            <span>已上传身份证</span>
            <label id="goodsReceipt_btn" class="goodsReceipt_label">+</label>
        </div>
<?php }?>
        </div>
        <label>*</label>
        <span id="addressdata[]"></span>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton( '保存',  ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['customers/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
<?php ActiveForm::end(); ?>
<?php \frontend\components\JsBlock::begin()?>
    <script>

        /*$(function(){

            //验证客户来源
            $("#customers-shop_id").blur(function(){
                if($.trim($(this).val()) == ''){
                    $("#customersshop_id").html('<label class="red">请选择客户来源</label>');
                    return false;
                }else{
                    $("#customersshop_id").text('请选择客户ID所注册的销售平台。');
                    return true;
                }
            });

            $(".orders-edbut").click(function(){
                if($.trim($('#customers-shop_id').val()) == ''){
                    $("#customersshop_id").html('<label class="red">请选择客户来源</label>');
                    return false;
                }
            });

        });*/
    </script>
<?php \frontend\components\JsBlock::end()?>