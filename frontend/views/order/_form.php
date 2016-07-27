<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;

$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);

$this->registerCssFile('@web/statics/css/css_global/global.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/sell.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);

/* @var $this yii\web\View */
/* @var $model frontend\models\Purchase */
/* @var $model frontend\models\PurchaseGoods */
/* @var $form yii\widgets\ActiveForm */

$user = new User();
$userinfo = $user->findIdentity(Yii::$app->session['__id']);
if(!empty($userinfo['store_id'])){
    $s_store_id = ' and store_id='.$userinfo['store_id'];
    $w_store_id = 'store_id='.$userinfo['store_id'];
}else{
    $s_store_id = '';
    $w_store_id = '';
}

$query = new \yii\db\Query();
$tablePrefix = Yii::$app->getDb()->tablePrefix;
//获取单位
$unit = $query->select('unit_id,unit')->from($tablePrefix.'unit')->all();
//获取仓库
$warehose = $query->select('warehouse_id,name')->from($tablePrefix.'warehouse')->where('status=1'.$s_store_id)->all();
$warehose_row = array();
if(!empty($warehose)){
    $warehose_row[''] = '请选择';
    foreach($warehose as $value){
        $warehose_row[$value['warehouse_id']] = $value['name'];
    }
}

//销售平台
$shop = $query->select('shop_id,name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('status=1'.$s_store_id)->all();
$shop_row = array();
if(!empty($shop)){
    $shop_row[''] = '请选择';
    foreach($shop as $value){
        $shop_row[$value['shop_id']] = $value['name'];
    }
}

$good_list = \frontend\components\Search::SearchGoods();

$stocks_list = \frontend\components\Search::SearchStocks('','');

$customers_list = \frontend\components\Search::SearchCustomers();

//获取物流公司
$delivery = $query->select('delivery_id,name')->from(Yii::$app->getDb()->tablePrefix.'expressway')->where('status=1'.$s_store_id)->all();
$delivery_row = array();
if(!empty($delivery)){
    $delivery_row[''] = '请选择';
    foreach($delivery as $value){
        $delivery_row[$value['delivery_id']] = $value['name'];
    }
}
?>
<?php $form = ActiveForm::begin([
    'id' => 'form-order',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
]); ?>
<?php if($model->isNewRecord){?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家ID-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?><!--创建时间-->
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['real_name']])?>
<?php }?>
    <h4 class="orders-newtade">订单基本信息</h4>
    <div class="orders-new clearfix">
        <p>仓库:</p>
        <?= $form->field($model, 'warehouse_id')->dropDownList($warehose_row,['onchange'=>"$('#order-warehouse_name').val($('#order-warehouse_id option:selected').text())"])->label(false)->hint('<label>* </label>') ?>
        <?= Html::activeHiddenInput($model,'warehouse_name')?>
    </div>
    <div class="orders-new clearfix">
        <p>销售平台:</p>
        <?= $form->field($model, 'shop_id')->dropDownList($shop_row,['onchange'=>"$('#order-shop_name').val($('#order-shop_id option:selected').text())"])->label(false)->hint('<label>* </label>') ?>
        <?= Html::activeHiddenInput($model,'shop_name')?>
    </div>
    <div class="orders-new clearfix">
        <p>订单编号:</p>
        <?= $form->field($model, 'order_no')->textInput()->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>销售日期:</p>
        <?= Html::activeInput('text',$model,'sale_time',['id'=>'pur-date','class'=>'laydate-icon','value'=>$model->sale_time>0?date('Y-m-d', $model->sale_time):date('Y-m-d', time())])?>
    </div>
    <h4 class="orders-newtade">商品相关信息*<span id="og-error"></span></h4>
    <div class="orders-new clearfix">
        <p>商品及赠品:</p>
        <div class="goodsReceipt" id="goodsReceipt_commoditybox">
            <?php if($model->isNewRecord){?>
                <div class="goodsReceipt_commodity">
                    <input type="hidden" id="ordergoods-goods_id" class="each_s" name="OrderGoods[goods_id][]" value="">
                    <input type="hidden" id="ordergoods-brand_id" name="OrderGoods[brand_id][]" value="">
                    <input type="hidden" id="ordergoods-unit_id" name="OrderGoods[unit_id][]" value="">
                    <input type="hidden" id="ordergoods-stocks_id" name="OrderGoods[stocks_id][]" value="">
                    <input type="hidden" id="ordergoods-batch_num1" name="OrderGoods[batch_num1][]" value="">
                    <input type="text" id="ordergoods-goods_name" name="OrderGoods[goods_name][]" value="" placeholder="商品中英文名称" class="goodsReceipt_commodity01">
                    <span class="goodsReceipt_commodity_span" style="width: 7%"><input type="text" id="ordergoods-spec" name="OrderGoods[spec][]" placeholder="规格" style="border: none;" value="" class="tet1" readonly="true"></span>
                    <span class="goodsReceipt_commodity_span" style="width: 13%"><input type="text" id="ordergoods-brand_name" name="OrderGoods[brand_name][]" placeholder="品牌" style="border: none; width: 100%" value="" class="tet2" readonly="true"></span>
                    <input id="ordergoods-batch_num" name="OrderGoods[batch_num][]" type="text" value="" placeholder="采购批号" class="goodsReceipt_commodity02">
                    <input id="ordergoods-sell_price" name="OrderGoods[sell_price][]" type="text" value="" placeholder="单价" class="goodsReceipt_commodity03 a1">
                    <span>元</span>
                    <input id="ordergoods-number" name="OrderGoods[number][]" type="text" value="" placeholder="数量" class="goodsReceipt_commodity03 a2" data="">
                    <span class="goodsReceipt_commodity_span_unit"><input type="text" id="ordergoods-unit_name" name="OrderGoods[unit_name][]" style='border: none; width: 100%' value="" placeholder="单位" readonly="true" /></span>
                    <label class="goodsReceipt_label" id = "goodsReceipt_commodity_btn">+</label>
                </div>
            <?php }else{?>
                <?php
                foreach($og_model as $k=>$v){
                    if(!empty($v['stocks_id'])){
                        $wstock['stocks_id'] =  $v['stocks_id'];
                        $stock_num = $query->select('stock_num')->from($tablePrefix.'stocks')->where($wstock)->one();
                    }
                    ?>
                    <div class="goodsReceipt_commodity">
                        <input type="hidden" id="ordergoods-id" name="OrderGoods[id][]" value="<?=$v['id']?>">
                        <input type="hidden" id="ordergoods-order_id" name="OrderGoods[order_id][]" value="<?=$v['order_id']?>">
                        <input type="hidden" id="ordergoods-goods_id" class="each_s"  name="OrderGoods[goods_id][]" value="<?=$v['goods_id']?>">
                        <input type="hidden" id="ordergoods-brand_id" name="OrderGoods[brand_id][]" value="<?=$v['brand_id']?>">
                        <input type="hidden" id="ordergoods-unit_id" name="OrderGoods[unit_id][]" value="<?=$v['unit_id']?>">
                        <input type="hidden" id="ordergoods-stocks_id" name="OrderGoods[stocks_id][]" value="<?=$v['stocks_id']?>">
                        <input type="hidden" id="ordergoods-batch_num1" name="OrderGoods[batch_num1][]" value="<?=$v['batch_num']?>">
                        <input type="text" id="ordergoods-goods_name" name="OrderGoods[goods_name][]" value="<?=$v['goods_name']?>" placeholder="商品中英文名称" class="goodsReceipt_commodity01">
                        <span class="goodsReceipt_commodity_span" style="width: 7%"><input type="text" id="ordergoods-spec" name="OrderGoods[spec][]" style="border: none;" placeholder="规格" value="<?=$v['spec']?>" class="tet1" readonly="true"></span>
                        <span class="goodsReceipt_commodity_span" style="width: 13%"><input type="text" id="ordergoods-brand_name" name="OrderGoods[brand_name][]" placeholder="品牌" style="border: none; width: 100%" value="<?=$v['brand_name']?>" class="tet2" readonly="true"></span>
                        <input id="ordergoods-batch_num" name="OrderGoods[batch_num][]" type="text" value="<?=$v['batch_num']?>" placeholder="采购批号" class="goodsReceipt_commodity02">
                        <input id="ordergoods-sell_price" name="OrderGoods[sell_price][]" type="text" value="<?=$v['sell_price']?>" placeholder="单价" class="goodsReceipt_commodity03 a1">
                        <span>元</span>
                        <input id="ordergoods-number" name="OrderGoods[number][]" type="text" value="<?=$v['number']?>" placeholder="数量" class="goodsReceipt_commodity03 a2" data="<?=!empty($stock_num['stock_num'])?$stock_num['stock_num']:''?>">
                        <span class="goodsReceipt_commodity_span_unit"><input type="text" id="ordergoods-unit_name" name="OrderGoods[unit_name][]" style='border: none; width:100%' placeholder="单位" value="<?=$v['unit_name']?>" readonly="true" /></span>
                        <?php if($k>0){?>
                            <label class="goodsReceipt_label" onclick="$(this).parent().remove();">-</label>
                        <?php }else{?>
                            <label class="goodsReceipt_label" id = "goodsReceipt_commodity_btn">+</label>
                        <?php }?>
                    </div>
                <?php }?>
            <?php }?>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>实收款:</p>
        <span style="display: none" id="t_real_pay"><?=$model->real_pay?></span>
        <?= $form->field($model, 'real_pay')->textInput(['onchange'=>"$('#order-discount').val(parseFloat($('#t_real_pay').text()-$('#order-real_pay').val()).toFixed(2))"])->label(false)->hint('<label>元 * 自动计算，允许修改。计算规则：所有商品的单价*数量之和。</label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>优惠:</p>
        <input type="text" id="order-discount" name="Order[discount]" style="border: none;" value="<?=!empty($model->discount)?$model->discount:0.00?>" readonly="readonly" />&nbsp;元
        <span>自动计算，计算规则：所有商品的单价*数量之和-实收款。</span>
    </div>
    <h4 class="orders-newtade">客户及收款信息</h4>
<?php
if($model->customer_id>0){
    $customers_info = $query->select('username,real_name')->from($tablePrefix.'customers')->where('customers_id='.$model->customer_id.$s_store_id)->one();
}
?>
    <div class="orders-new clearfix">
        <p>客户帐号:</p>
        <?= $form->field($model, 'customer_name')->textInput(['value'=>!empty($customers_info)?$customers_info['username']:''])->label(false)->hint('<label>* </label>') ?>
        <strong class="iconfont add_btns" style="cursor: pointer">&#xe620;</strong>
        <?= Html::activeHiddenInput($model,'customer_id')?>
        <span id="c_exist" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <lable id="customers-real_name" style="line-height: 32px"><?=!empty($customers_info)?$customers_info['real_name']:''?></lable>
    </div>
    <div class="orders-new clearfix">
        <p>收货人姓名:</p>
        <label id="c_address" style="margin: 0">
            <select id="order-address_id" name="Order[address_id]" onchange="$('#address-accept_mobile').text($('#order-address_id option:selected').attr('a1'));$('#address-accept_address').text($('#order-address_id option:selected').attr('a2'));$('#address-accept_idcard').text($('#order-address_id option:selected').attr('a3'));$('#address-is_idcard').text($('#order-address_id option:selected').attr('a4'));$('#accept_name').val($('#order-address_id option:selected').attr('a5'));">
                <option a1="" a2="" a3="" a4="" a5="" value="">请选择</option>
                <?php if($model->customer_id>0){?>
                    <?php
                    $addresslist = (new \yii\db\Query())->select('*')->from($tablePrefix.'address')->where('customers_id='.$model->customer_id)->orderBy(['create_time'=>SORT_ASC])->all();
                    foreach($addresslist as $v){
                        ?>
                        <option a1="<?=!empty($v['accept_mobile'])?$v['accept_mobile']:''?>" a2="<?=!empty($v['accept_address'])?$v['accept_address']:''?>" a3="<?=!empty($v['accept_idcard'])?$v['accept_idcard']:''?>" a4="<?=!empty($v['is_idcard'])?'是':'否'?>" a5="<?=!empty($v['accept_name'])?$v['accept_name']:''?>" value="<?=$v['address_id']?>" <?php if($v['address_id']==$model->address_id){echo 'selected';};?>><?=$v['accept_name']?></option>
                    <?php }?>
                <?php }?>
            </select>
        </label>
        <label>*</label>
        <span id="orderaddress_id" style="font-weight: bold; color: red; margin: 0"></span>
    </div>
<?php
if($model->address_id>0){
    $addressinfo = $query->select('accept_name,accept_mobile,accept_address,accept_idcard,is_idcard,idcard_url')->from($tablePrefix.'address')->where('address_id='.$model->address_id)->one();
}
?>
    <input type="hidden" id="accept_name" name="accept_name" value="<?=!empty($addressinfo['accept_name'])?$addressinfo['accept_name']:''?>" />
    <div class="orders-new clearfix">
        <p>电话:</p>
        <label id="address-accept_mobile"><?=!empty($addressinfo['accept_mobile'])?$addressinfo['accept_mobile']:''?></label>
    </div>
    <div class="orders-new clearfix">
        <p>地址:</p>
        <label id="address-accept_address"><?=!empty($addressinfo['accept_address'])?$addressinfo['accept_address']:''?></label>
    </div>
    <div class="orders-new clearfix">
        <p>身份证号码:</p>
        <label id="address-accept_idcard"><?=!empty($addressinfo['accept_idcard'])?$addressinfo['accept_idcard']:''?></label>
    </div>
    <div class="orders-new clearfix">
        <p>是否已上传身份证:</p>
        <label id="address-is_idcard"><?=!empty($addressinfo['is_idcard']) ? '是' : '否'?></label>
    </div>
    <div class="orders-new clearfix">
        <p>身份证上传网址:</p>
        <input type="button" value="生成" id="cardUrl" style="width: 100px;"/>
        <input type="button" onclick="jsCopy();" value="复制网址" style="width: 100px; margin-left: 20px;"/>
        <input type="text" id="address-idcard_url" name="Address[idcard_url]" placeholder="" value="<?=!empty($addressinfo['idcard_url'])?$addressinfo['idcard_url']:''?>" style=" border:none;" readOnly="true">
    </div>
    <div class="orders-new clearfix">
        <p>物流公司:</p>
        <?= $form->field($model, 'delivery_id')->dropDownList($delivery_row,['onchange'=>"$('#order-delivery_name').val($('#order-delivery_id option:selected').text())"])->label(false)->hint('<label> </label>') ?>
        <?= Html::activeHiddenInput($model,'delivery_name')?>
    </div>
    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['order/index'])?>">
            <span class="orders-newbut2">返回</span>
        </a>
    </div>
<?php ActiveForm::end(); ?>
<?php
//获取客户来源
$shopList = (new \yii\db\Query())->select('shop_id,name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('status=1'.$s_store_id)->all();

?>
    <div class="new_panel">
        <h2>新建客户<span class="iconfont icon_close">&#xe608;</span></h2>
        <div>
            <p class="new_panel_title">客户基本信息</p>
            <div class="infor_box">
                <span>客户来源：</span>
                <select id="customers-shop_id" name="Customers[shop_id]" onchange="$('#customers-shop_name').val($('#customers-shop_id option:selected').text())">
                    <option value="">请选择</option>
                    <?php foreach($shopList as $value){?>
                    <option value="<?=$value['shop_id']?>"><?=$value['name']?></option>
                    <?php }?>
                </select>
                <input type="hidden" id="customers-shop_name" name="Customers[shop_name]" value="" />
                <lable id="ca_sid">*</lable>
            </div>
            <div class="infor_box">
                <span>客户账号：</span>
                <input type="text" id="customers-username" name="Customers[username]" value="">
                <lable id="ca_cun">*</lable>
            </div>
            <div class="infor_box">
                <span>姓名：</span>
                <input type="text" id="customers-ca_real_name" name="Customers[ca_real_name]" value="">
                <lable id="ca_crn">*</lable>
            </div>
            <div class="infor_checkbox">
                <span>性别：</span>
                <span><input type="radio" id="customers-sex" name="Customers[sex]" value="1" checked>男</span>
                <span><input type="radio" id="customers-sex" name="Customers[sex]" value="0">女</span>
                <span><input type="radio" id="customers-sex" name="Customers[sex]" value="2">保密</span>
            </div>
            <div class="infor_box">
                <span>联系电话：</span>
                <input type="text" id="customers-mobile" name="Customers[mobile]" value="">
                <lable id="ca_cm">*</lable>
            </div>
            <div class="infor_box">
                <span>Email/QQ/其他：</span>
                <input type="text" id="customers-other" name="Customers[other]" value="">
            </div>
            <div class="infor_box">
                <span>地址：</span>
                <input type="text" id="customers-address" name="Customers[address]" value="">
            </div>
            <div class="infor_checkbox">
                <span>客户类型：</span>
                <span><input type="radio" id="customers-type" name="Customers[type]" value="0" checked>个人客户</span>
                <span><input type="radio" id="customers-type" name="Customers[type]" value="1">企业客户</span>
            </div>
            <div class="infor_box">
                <span>顺序：</span>
                <input type="text" id="customers-sort" name="Customers[sort]" value="999">
                <lable>*</lable>
            </div>
            <div class="remark_box">
                <span>备注说明：</span>
                <textarea id="customers-remark" name="Customers[remark]"></textarea>
            </div>
            <p class="new_panel_title">收获信息</p>
            <div class="bottom_box">
                <span class="bottom_left">收货人信息：</span>
                <div class="bottom_right">
                    <input type="text" name="Address_ca[accept_name]" placeholder="姓名*（假设增加姓名李四，注：本括号内不属于本框提示内容）" value="" /><label id="ca_aan"></label><br />
                    <input type="text" name="Address_ca[accept_mobile]" placeholder="电话*" value="" /><lable id="ca_aam"></lable><br />
                    <input type="text" name="Address_ca[accept_address]" value="" placeholder="收货地址*" value="" /><lable id="ca_aaa"></lable><br />
                    <input type="text" name="Address_ca[zcode]" placeholder="邮政编码" value="" /><br />
                    <input type="text" name="Address_ca[accept_idcard]" placeholder="身份证号码" value="" /><br />
                    <span class="infor_checkbox"><input type="checkbox" name="Address_ca[is_idcard]" value="1" />已上传身份证</span>
                </div>
                <div class="clear"></div>
            </div>
            <div class="button">
                <button class="button_1">保存</button>
                <button class="button_2">关闭</button>
            </div>
        </div>
    </div>

<?php \frontend\components\JsBlock::begin()?>
<script>
$(function(){

    var closepanel;
    //点击新增客户弹窗
    $('.add_btns').click(function(){
        closepanel = $('.new_panel').bPopup({
            positionStyle: 'fixed'
        });
    });
    //点击关闭
    $('.icon_close,.button_2').click(function(){
        closepanel.close();
    });
    //验证客户来源
    $("#customers-shop_id").focus(function(){
        $("#ca_sid").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_sid").html('<strong class="red">请选择客户来源</strong>');
            return false;
        }
    });
    //验证客户帐号
    $("#customers-username").focus(function(){
        $("#ca_cun").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_cun").html('<strong class="red">客户帐号不能为空</strong>');
            return false;
        }
    });
    //验证姓名
    $("#customers-ca_real_name").focus(function(){
        $("#ca_crn").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_crn").html('<strong class="red">姓名不能为空</strong>');
            return false;
        }
    });
    //验证联系电话
    $("#customers-mobile").focus(function(){
        $("#ca_cm").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_cm").html('<strong class="red">联系电话不能为空</strong>');
            return false;
        }
    });
    //验证收货人姓名
    $("input[name='Address_ca[accept_name]']").focus(function(){
        $("#ca_aan").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_aan").html('<strong class="red">收货人姓名不能为空</strong>');
            return false;
        }
    });
    //验证收货人电话
    $("input[name='Address_ca[accept_mobile]']").focus(function(){
        $("#ca_aam").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_aam").html('<strong class="red">收货人电话不能为空</strong>');
            return false;
        }
    });
    //验证收货人地址
    $("input[name='Address_ca[accept_address]']").focus(function(){
        $("#ca_aaa").html('');
    }).blur(function(){
        if($(this).val()==''){
            $("#ca_aaa").html('<strong class="red">收货人地址不能为空</strong>');
            return false;
        }
    });
    //点击保存按钮执行的事件
    $('.button_1').click(function(){
        //后台需要执行的事件
        $.ajax({
            type: 'POST',
            url: "<?=Url::to(['order/index'])?>",
            data:{
                action: 'ajaxOca',
                Customers:{
                    shop_id:$("#customers-shop_id").val(),
                    shop_name:$("#customers-shop_name").val(),
                    username: $.trim($("input[name='Customers[username]").val()),
                    real_name:$.trim($("input[name='Customers[ca_real_name]']").val()),
                    sex:$("input[name='Customers[sex]']").val(),
                    mobile: $.trim($("input[name='Customers[mobile]']").val()),
                    other: $.trim($("input[name='Customers[other]']").val()),
                    address: $.trim($("input[name='Customers[address]']").val()),
                    type:$("input[name='Customers[type]']").val(),
                    sort: $.trim($("input[name='Customers[sort]']").val()),
                    remark: $.trim($("input[name='Customers[remark]']").val())
                },
                Address:{
                    accept_name: $.trim($("input[name='Address_ca[accept_name]']").val()),
                    accept_mobile: $.trim($("input[name='Address_ca[accept_mobile]']").val()),
                    accept_address: $.trim($("input[name='Address_ca[accept_address]']").val()),
                    zcode: $.trim($("input[name='Address_ca[zcode]").val()),
                    accept_idcard: $.trim($("input[name='Address_ca[accept_idcard]").val()),
                    is_idcard:$("input[name='Address_ca[is_idcard]").val()
                },
                Order:{
                    store_id: $("input[name='Order[store_id]']").val(),
                    store_name: $("input[name='Order[store_name]']").val(),
                    shop_id: $("input[name='Order[shop_id]']").val(),
                    shop_name: $("input[name='Order[shop_name]']").val(),
                    add_user_id: $("input[name='Order[add_user_id]']").val(),
                    add_user_name: $("input[name='Order[add_user_name]']").val(),
                    create_time: $("input[name='Order[create_time]']").val()
                }
            },
            success: function( data ) {
                if(data.status==1){
                    $("#ca_sid").html('<strong class="red">'+data.msg+'</strong>');
                    return false;
                }else if(data.status==2){
                    $("#ca_cun").html('<strong class="red">'+data.msg+'</strong>');
                    return false;
                }else if(data.status==3){
                    $("#ca_crn").html('<strong class="red">'+data.msg+'</strong>');
                    return false;
                }else if(data.status==4){
                    $("#ca_cm").html('<strong class="red">'+data.msg+'</strong>');
                    return false;
                }else if(data.status==5){
                    $("#ca_aan").html('<strong class="red">&nbsp;'+data.msg+'</strong>');
                    return false;
                }else if(data.status==6){
                    $("#ca_aam").html('<strong class="red">&nbsp;'+data.msg+'</strong>');
                    return false;
                }else if(data.status==7){
                    $("#ca_aaa").html('<strong class="red">&nbsp;'+data.msg+'</strong>');
                    return false;
                }else if(data.status==8){
                    $("#ca_cun").html('<strong class="red">'+data.msg+'</strong>');
                    return false;
                }else if(data.status==9){
                    $("#ca_aan").html('<strong class="red">&nbsp;'+data.msg+'</strong>');
                    return false;
                }else if(data.status==10){
                    //alert(data.msg);
                    $("#order-customer_id").val(data.row.customers_id);
                    $("#order-customer_name").val(data.row.customer_name);
                    $("#customers-real_name").text(data.row.real_name);
                    $("#c_address").html(data.address_id);
                    $("#accept_name").val(data.row.accept_name);
                    $("#address-accept_mobile").text(data.row.accept_mobile);
                    $("#address-accept_address").text(data.row.accept_address);
                    $("#address-accept_idcard").text(data.row.accept_idcard);
                    $("#address-is_idcard").text(data.row.is_idcard);

                    closepanel.close(); //关闭弹窗
                    return false;
                }
            },
            dataType: "json"
        });
    });

    $('#goodsReceipt_commodity_btn').click(function(){
        var $html = $('<div class="goodsReceipt_commodity">' +
            '<input type="hidden" id="ordergoods-id" name="OrderGoods[id][]" value="">' +
            '<input type="hidden" id="ordergoods-goods_id" class="each_s" name="OrderGoods[goods_id][]" value="">' +
            '<input type="hidden" id="ordergoods-brand_id" name="OrderGoods[brand_id][]" value="">' +
            '<input type="hidden" id="ordergoods-unit_id" name="OrderGoods[unit_id][]" value="">' +
            '<input type="hidden" id="ordergoods-stocks_id" name="OrderGoods[stocks_id][]" value="">' +
            '<input type="hidden" id="ordergoods-batch_num1" name="OrderGoods[batch_num1][]" value="">' +
            '<input type="text" id="ordergoods-goods_name" name="OrderGoods[goods_name][]" placeholder="商品中英文名称" class="goodsReceipt_commodity01" value="">' +
            '<span class="goodsReceipt_commodity_span" style="width: 7%"><input type="text" id="ordergoods-spec" name="OrderGoods[spec][]" style="border: none;" placeholder="规格" readonly="true" class="tet1" value=""></span>' +
            '<span class="goodsReceipt_commodity_span" style="width: 13%"><input type="text" id="ordergoods-brand_name" name="OrderGoods[brand_name][]" placeholder="品牌" style="border: none; width: 100%" readonly="true" class="tet2" value=""></span>' +
            '<input id="ordergoods-batch_num" name="OrderGoods[batch_num][]" type="text" placeholder="采购批号" class="goodsReceipt_commodity02" value="">' +
            '<input id="ordergoods-sell_price" name="OrderGoods[sell_price][]" type="text" value="" placeholder="单价" class="goodsReceipt_commodity03 kk a1">' +
            '<span>元</span>' +
            '<input id="ordergoods-number" name="OrderGoods[number][]" type="text" placeholder="数量" class="goodsReceipt_commodity03 kk a2" value="" data="">' +
            '<span class="goodsReceipt_commodity_span_unit"><input type="text" id="ordergoods-unit_name" name="OrderGoods[unit_name][]" style="border: none; width: 100%" placeholder="单位" value="" readonly="true" value="" /></span>' +
            '<label class="goodsReceipt_label jj">-</label></div>');

        $("#goodsReceipt_commoditybox").append($html);

        //添加后需要给文本框绑定autocomplete
        bindAutocomplete($html.find('.goodsReceipt_commodity01'));
        batchAutocomplete($html.find('.goodsReceipt_commodity02'));

        //失去焦点计算总金额
        $(".goodsReceipt_commodity03").blur(function () {
            var texts = $(".goodsReceipt_commodity03"),
                firstValue = null,
                secondValue = null,
                sum = 0;

            for (var i = 0, len = texts.length; i < len; i = i + 2) {
                firstValue = parseFloat($(texts[i]).val() || 0);//"||"表示如果前面的值为空，就赋后面的值
                secondValue = parseInt($(texts[i + 1]).val() || 0);

                sum += firstValue * secondValue;
            }
            $("#order-real_pay").val(parseFloat(sum).toFixed(2));
            $("#t_real_pay").text(parseFloat(sum).toFixed(2));
        });

    });

    //在添加的文本框上绑定autocomplete
    function bindAutocomplete($input){
        $input.bind( "focusin keyup", function( event ) {
            gname = $(event.target).val();
        });
        $input.autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "<?=Url::to(['order/index'])?>",
                    data:{
                        action: 'goods_info',
                        warehouse_id:$("#order-warehouse_id").val(),
                        goods_name:gname
                    },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                value: item.goods_name,
                                goods_name:item.goods_name,
                                spec:item.spec,
                                brand_name:item.brand_name,
                                unit_name:item.unit_name,
                                goods_id:item.goods_id,
                                unit_id:item.unit_id,
                                brand_id:item.brand_id
                            }
                        }));
                    }
                });
            },
            minLength:0,
            scroll: true,
            select: function(event, ui){
                $(this).prevAll('#ordergoods-goods_id').val(ui.item.goods_id);
                $(this).prevAll('#ordergoods-brand_id').val(ui.item.brand_id);
                $(this).prevAll('#ordergoods-unit_id').val(ui.item.unit_id);
                $(this).nextAll('.goodsReceipt_commodity_span').find('.tet1').val(ui.item.spec);
                $(this).nextAll('.goodsReceipt_commodity_span').find('.tet2').val(ui.item.brand_name);
                $(this).nextAll('.goodsReceipt_commodity_span_unit').find('input').val(ui.item.unit_name);
                //重新选择清空批号
                $(this).nextAll('.goodsReceipt_commodity02').val('');
            }
        });
        $input.focus(function(){
            if($(this).val() == ""){
                $input.autocomplete("search", "");
            }
        });

    }

    function batchAutocomplete($input){

        $input.autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "<?=Url::to(['order/index'])?>",
                    dataType: "json",
                    data:{
                        action: 'f_batch',
                        warehouse_id:$("#order-warehouse_id").val(),
                        goods_id:$input.prevAll('.each_s').val()
                    },
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                stocks_id:item.stocks_id,
                                value: item.batch_num+'(库存'+item.stock_num+item.unit_name+')',
                                stock_num:item.stock_num,
                                batch_num1:item.batch_num
                            }
                        }));
                    }
                });
            },
            minLength:0,
            scroll: true,
            select: function(event, ui){
                $(this).prevAll('#ordergoods-stocks_id').val(ui.item.stocks_id);
                $(this).nextAll('#ordergoods-number').attr('data',ui.item.stock_num);
                $(this).prevAll('#ordergoods-batch_num1').val(ui.item.batch_num1);

                $(event.target).blur(function(){
                    $(event.target).val(ui.item.batch_num1);
                })
            }
        });
        $input.focus(function(){
            if($(this).val() == ""){
                $input.autocomplete("search", "");
            }
        });
    }

    //默认第一个文本框绑定autocomplete
    bindAutocomplete($(".goodsReceipt_commodity01"));
    //batchAutocomplete($(".goodsReceipt_commodity02"));

    $(".goodsReceipt_commodity02").each(function(index) {
        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "<?=Url::to(['order/index'])?>",
                    dataType: "json",
                    data:{
                        action: 'f_batch',
                        warehouse_id:$("#order-warehouse_id").val(),
                        goods_id:$('.each_s').eq(index).val()
                    },
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                stocks_id:item.stocks_id,
                                value: item.batch_num+'(库存'+item.stock_num+item.unit_name+')',
                                stock_num:item.stock_num,
                                batch_num1:item.batch_num
                            }
                        }));
                    }
                });
            },
            minLength:0,
            scroll: true,
            select: function(event, ui){
                $(this).prevAll('#ordergoods-stocks_id').val(ui.item.stocks_id);
                $(this).nextAll('#ordergoods-number').attr('data',ui.item.stock_num);
                $(this).prevAll('#ordergoods-batch_num1').val(ui.item.batch_num1);

                $(event.target).blur(function(){
                    $(event.target).val(ui.item.batch_num1);

                    $.post("<?=Url::to(['order/index'])?>",{action:'b_batchnum',batch_num:ui.item.batch_num1},function(result){
                        if(result!=0){
                            $("#og-error").html('<lable class="red"></lable>');
                            $('.orders-edbut').attr('disabled',false);
                        }else{
                            $("#og-error").html('<label class="red">批号不存在</label>');
                            //$('.orders-edbut').attr('disabled',true);
                            return false;
                        }
                    },'json');


                })
            }
        });
        $(this).focus(function(){
            if($(this).val() == ""){
                $(this).autocomplete("search", "");
            }
        });
    });

    $('#goodsReceipt_commoditybox').on('click','.jj',function(){
        jd(this);
        $(this).parent().remove();
    });

    function jd(obj){
        var oneVal = parseFloat($(obj).prevAll('input.kk').eq(0).val());
        var twoVal = parseFloat($(obj).prevAll('input.kk').eq(1).val());
        var h =  oneVal * twoVal;
        if(isNaN(h)){
            h = 0;
        }
        $('#order-real_pay').val(parseFloat($('#order-real_pay').val() - h).toFixed(2));
    }

    $("input[name='Order[customer_name]']").bigAutocomplete({
        width:200,data:[
            <?php foreach($customers_list as $v){?>
            {title:"<?=$v['username']?>",result:{customers_id:"<?=$v['customers_id']?>",real_name:"<?=$v['real_name']?>"}},
            <?php }?>
        ],
        callback:function(data){
            $("input[name='Order[customer_id]']").val(data.result.customers_id);
            $("#customers-real_name").text(data.result.real_name);
            //根据帐号调整收货人信息
            $.post("<?=Url::to(['order/index'])?>",{action:'address_info',customers_id:data.result.customers_id},function(result){

                $("#c_exist").text('');
                $('.orders-edbut').attr('disabled',false);

                var html = '';
                html += '<select id="order-address_id" name="Order[address_id]" onchange=\'$("#address-accept_mobile").text($("#order-address_id option:selected").attr("a1"));$("#address-accept_address").text($("#order-address_id option:selected").attr("a2"));$("#address-accept_idcard").text($("#order-address_id option:selected").attr("a3"));$("#address-is_idcard").text($("#order-address_id option:selected").attr("a4"));$("#accept_name").val($("#order-address_id option:selected").attr("a5"));\'>';
                html += '<option a1="" a2="" a3="" a4="" a5="" value="">请选择</option>';
                for(var i=0; i<result.length; i++){
                    if(result[i].is_idcard==0){
                        var is_idcard = '否';
                    }else{
                        var is_idcard = '是';
                    }

                    html += '<option a1='+result[i].accept_mobile+' a2="'+result[i].accept_address+'" a3="'+result[i].accept_idcard+'" a4="'+is_idcard+'" a5="'+result[i].accept_name+'" value='+result[i].address_id+'>'+result[i].accept_name+'</option>';
                }
                html += '</select>';
                $("#c_address").html(html);
            },"json");
        }
    });

    //失去焦点计算总金额
    $(".goodsReceipt_commodity03").blur(function () {
        var texts = $(".goodsReceipt_commodity03"),
            firstValue = null,
            secondValue = null,
            sum = 0;

        for (var i = 0, len = texts.length; i < len; i = i + 2) {
            firstValue = parseFloat($(texts[i]).val() || 0);//"||"表示如果前面的值为空，就赋后面的值
            secondValue = parseInt($(texts[i + 1]).val() || 0);

            sum += firstValue * secondValue;
        }
        $("#order-real_pay").val(parseFloat(sum).toFixed(2));
        $("#t_real_pay").text(parseFloat(sum).toFixed(2));
    });

    //验证商品中英文名称
    $('.goodsReceipt').on('blur','#ordergoods-goods_name',function(){
        if($.trim($(this).val()) == ''){
            $("#og-error").html('<label class="red">商品中英文名称不能为空</label>');
            return false;
        }else{
            $.post("<?=Url::to(['order/index'])?>",{action:'gname',goods_name:$.trim($(this).val())},function(result){
                if(result==0){
                    $("#og-error").html('<label class="red">商品中英文名称不存在</label>');
                    //$('.orders-edbut').attr('disabled',true);
                    return false;
                }else{
                    $("#og-error").html('');
                    $('.orders-edbut').attr('disabled',false);
                }
            },'json');
        }
    });

    //验证采购批号
    $('.goodsReceipt').on('blur','#ordergoods-batch_num',function(){
        if($.trim($(this).val()) == ''){
            $("#og-error").html('<label class="red">采购批号不能为空</label>');
            return false;
        }else{
            $.post("<?=Url::to(['order/index'])?>",{action:'b_batchnum',batch_num:$.trim($(this).val())},function(result){
                if(result!=0){
                    $("#og-error").html('<lable class="red"></lable>');
                    $('.orders-edbut').attr('disabled',false);
                }else{
                    $("#og-error").html('<label class="red">批号不存在</label>');
                    //$('.orders-edbut').attr('disabled',true);
                    return false;
                }
            },'json');
        }

    });

    //验证单价
    $('.goodsReceipt').on('blur','#ordergoods-sell_price',function(){
        if($.trim($(this).val()) == ''){
            $("#og-error").html('<label class="red">单价不能为空</label>');
            return false;
        }else{
            if(isNaN($.trim($(this).val())) == true){
                $("#og-error").html('<label class="red">单价必须为数字</label>');
                return false;
            }else{
                if($.trim($(this).val())<0){
                    $("#og-error").html('<label class="red">单价不能小于0</label>');
                    return false;
                }else{
                    $("#og-error").text('');
                    return true;
                }
            }
        }

    });

    //验证数量
    $('.goodsReceipt').on('blur','#ordergoods-number',function(){
        if($.trim($(this).val()) == ''){
            $("#og-error").html('<label class="red">数量不能为空</label>');
            return false;
        }else{
            if(isNaN($.trim($(this).val())) == true){
                $("#og-error").html('<label class="red">数量必须为数字</label>');
                return false;
            }else{
                var stock_num = $(this).attr('data');
                if($.trim($(this).val())<=0 || $.trim($(this).val()) > parseInt(stock_num)){
                    $("#og-error").html('<label class="red">数量不能小于等于0或大于此批号库存数</label>');
                    return false;
                }else{
                    $("#og-error").text('');
                    return true;
                }

            }
        }
    });

    //验证客户帐号是否存在
    $("input[name='Order[customer_name]']").keyup(function(){
        var customer_name = $.trim($(this).val());
        $.post("<?=Url::to(['order/index'])?>",{action:'c_exist',customer_name:customer_name},function(result){
            if(result==0){
                $("#c_exist").text('客户帐号不存在');
                $('.orders-edbut').attr('disabled',true);
                return false;
            }else{
                $("input[name='Order[customer_id]']").val(result.customers_id);
                $("#customers-real_name").text(result.real_name);
                //根据帐号调整收货人信息
                $.post("<?=Url::to(['order/index'])?>",{action:'address_info',customers_id:result.customers_id},function(result){

                    var html = '';
                    html += '<select id="order-address_id" name="Order[address_id]" onchange=\'$("#address-accept_mobile").text($("#order-address_id option:selected").attr("a1"));$("#address-accept_address").text($("#order-address_id option:selected").attr("a2"));$("#address-accept_idcard").text($("#order-address_id option:selected").attr("a3"));$("#address-is_idcard").text($("#order-address_id option:selected").attr("a4"));$("#accept_name").val($("#order-address_id option:selected").attr("a5"));\'>';
                    html += '<option a1="" a2="" a3="" a4="" a5="" value="">请选择</option>';
                    for(var i=0; i<result.length; i++){
                        if(result[i].is_idcard==''){
                            is_idcard = '否';
                        }else{
                            is_idcard = '是';
                        }
                        html += '<option a1='+result[i].accept_mobile+' a2="'+result[i].accept_address+'" a3="'+result[i].accept_idcard+'" a4="'+is_idcard+'" a5="'+result[i].accept_name+'" value='+result[i].address_id+'>'+result[i].accept_name+'</option>';
                    }
                    html += '</select>';
                    $("#c_address").html(html);
                },"json");

                $("#c_exist").text('');
                $('.orders-edbut').attr('disabled',false);
            }
        },'json');

    });

    $(".orders-edbut").click(function(){

        for(var i=0;i< $('.goodsReceipt_commodity01').length;i++){
            var goods_name = $('.goodsReceipt_commodity01').eq(i).val();
            var batch_num = $('.goodsReceipt_commodity02').eq(i).val();
            var sell_price = $.trim($('.a1').eq(i).val());
            var number = $.trim($('.a2').eq(i).val());
            var stocks_num = $('.a2').eq(i).attr('data');
            if(goods_name == ''){
                $("#og-error").html('<label class="red">商品中英文名称不能为空</label>');
                return false;
            }
            if(batch_num == ''){
                $("#og-error").html('<label class="red">采购批号不能为空</label>');
                return false;
            }
            if(sell_price == ''){
                $("#og-error").html('<label class="red">单价不能为空</label>');
                return false;
            }
            if(isNaN(sell_price) == true){
                $("#og-error").html('<label class="red">单价必须为数字</label>');
                return false;
            }
            if(sell_price<0){
                $("#og-error").html('<label class="red">单价不能小于0</label>');
                return false;
            }
            if(number == ''){
                $("#og-error").html('<label class="red">数量不能为空</label>');
                return false;
            }
            if(isNaN(number) == true){
                $("#og-error").html('<label class="red">数量必须为数字</label>');
                return false;
            }
            if(number<=0 || number>parseInt(stocks_num)){
                $("#og-error").html('<label class="red">数量不能小于等于0或大于此批号库存数</label>');
                return false;
            }
        }
        //验证收货人姓名
        if($("select[name='Order[address_id]']").val()==''){
            $("#orderaddress_id").text('请选择收货人姓名');
            return false;
        }else{
            $("#orderaddress_id").text('');
        }

    });

});

    //生成身份证上传地址
    $("#cardUrl").click(function(){
        $.post("<?=Url::to(['order/index'])?>",{action:'cardUrl',accept_mobile:$('#address-accept_mobile').text(),accept_name:$('#accept_name').val(),order_no:$('#order-order_no').val(),shop_name:$('#order-shop_name').val()},function(result){

            if($('#accept_name').val()==''){
                alert("请先选择收货人姓名");
                return false;
            }
            if($('#order-order_no').val()==''){
                alert("请先填写订单编号");
                return false;
            }
            if($('#order-shop_id').val()==''){
                alert("请先选择销售平台");
                return false;
            }

            $("#address-idcard_url").val(result);
        },'json');
    });
    function jsCopy(){
        var e=document.getElementById("address-idcard_url");//对象是contents
        e.select(); //选择对象
        document.execCommand("Copy"); //执行浏览器复制命令
    }
    </script>
<?php \frontend\components\JsBlock::end()?>