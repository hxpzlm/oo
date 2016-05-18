<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;

$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);

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
?>
<?php $form = ActiveForm::begin(); ?>
<?php if($model->isNewRecord){?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家ID-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?><!--创建时间-->
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['username']])?>
<?php }?>
    <h4 class="orders-newtade">订单基本信息</h4>
    <div class="orders-new clearfix">
        <p>仓库:</p>
        <?= $form->field($model, 'warehouse_id')->dropDownList($warehose_row,['onchange'=>"$('#order-warehouse_name').val($('#order-warehouse_id option:selected').text())"])->label(false)->hint('<label>* </label>') ?>
        <?= Html::activeHiddenInput($model,'warehouse_name')?>
    </div>
    <div class="orders-new clearfix">
        <p>销售平台:</p>
        <?= $form->field($model, 'shop_id')->dropDownList($shop_row,['onchange'=>"$('#order-shop_name').val($('#order-shop_id option:selected').attr('title'))"])->label(false)->hint('<label>* </label>') ?>
        <?= Html::activeHiddenInput($model,'shop_name')?>
    </div>
    <div class="orders-new clearfix">
        <p>订单编号:</p>
        <?= $form->field($model, 'order_no')->textInput()->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>销售日期:</p>
        <?= Html::activeInput('text',$model,'sale_time',['id'=>'pur-date','class'=>'laydate-icon','value'=>$model->sale_time>0?date('Y-m-d', $model->sale_time):''])?>
    </div>
    <h4 class="orders-newtade">商品相关信息</h4>
    <div class="orders-new clearfix">
        <p>商品及赠品:</p>
        <div class="goodsReceipt" id="goodsReceipt_commoditybox">
        <?php if($model->isNewRecord){?>
            <div class="goodsReceipt_commodity">
                <input type="hidden" id="ordergoods-goods_id" name="OrderGoods[goods_id][]" value="">
                <input type="hidden" id="ordergoods-brand_id" name="OrderGoods[brand_id][]" value="">
                <input type="hidden" id="ordergoods-unit_id" name="OrderGoods[unit_id][]" value="">
                <input type="text" id="ordergoods-goods_name" name="OrderGoods[goods_name][]" value="" placeholder="商品中英文名称" class="goodsReceipt_commodity01">
                <span class="goodsReceipt_commodity_span"><input type="text" name="OrderGoods[spec][]" placeholder="规格" style="border: none;" value="" readonly="true"></span>
                <span class="goodsReceipt_commodity_span"><input type="text" name="OrderGoods[brand_name][]" placeholder="品牌" style="border: none;" readonly="true"></span>
                <input id="ordergoods-batch_num" name="OrderGoods[batch_num][]" type="text" value="" placeholder="采购批号" class="goodsReceipt_commodity02">
                <input type="hidden" id="ordergoods-stocks_id" name="OrderGoods[stocks_id][]" value="">
                <input id="ordergoods-sell_price" name="OrderGoods[sell_price][]" type="text" value="" placeholder="单价" class="goodsReceipt_commodity03">
                <span>元</span>
                <input id="ordergoods-number" name="OrderGoods[number][]" type="text" value="" placeholder="数量" class="goodsReceipt_commodity03" onblur="price_n(this.value)">
                <span class="goodsReceipt_commodity_span_unit"><input type="text" name="OrderGoods[unit_name][]" style='border: none;' placeholder="" value="" readonly="true" /></span>
                <label class="goodsReceipt_label" id = "goodsReceipt_commodity_btn">+</label>
            </div>
        <?php }else{?>
        <?php foreach($og_model as $k=>$v){?>
            <div class="goodsReceipt_commodity">
                <input type="hidden" id="ordergoods-id" name="OrderGoods[id][]" value="<?=$v['id']?>">
                <input type="hidden" id="ordergoods-order_id" name="OrderGoods[order_id][]" value="<?=$v['order_id']?>">
                <input type="hidden" id="ordergoods-goods_id" name="OrderGoods[goods_id][]" value="<?=$v['goods_id']?>">
                <input type="hidden" id="ordergoods-brand_id" name="OrderGoods[brand_id][]" value="<?=$v['brand_id']?>">
                <input type="hidden" id="ordergoods-unit_id" name="OrderGoods[unit_id][]" value="<?=$v['unit_id']?>">
                <input type="text" id="ordergoods-goods_name" name="OrderGoods[goods_name][]" value="<?=$v['goods_name']?>" placeholder="商品中英文名称" class="goodsReceipt_commodity01">
                <span class="goodsReceipt_commodity_span"><input type="text" name="OrderGoods[spec][]" style="border: none;" placeholder="规格" value="<?=$v['spec']?>" readonly="true"></span>
                <span class="goodsReceipt_commodity_span"><input type="text" name="OrderGoods[brand_name][]" placeholder="品牌" style="border: none;" value="<?=$v['brand_name']?>" readonly="true"></span>
                <input id="ordergoods-batch_num" name="OrderGoods[batch_num][]" type="text" value="<?=$v['batch_num']?>" placeholder="采购批号" class="goodsReceipt_commodity02">
                <input type="hidden" id="ordergoods-stocks_id" name="OrderGoods[stocks_id][]" value="<?=$v['stocks_id']?>">
                <input id="ordergoods-sell_price" name="OrderGoods[sell_price][]" type="text" value="<?=$v['sell_price']?>" placeholder="单价" class="goodsReceipt_commodity03">
                <span>元</span>
                <input id="ordergoods-number" name="OrderGoods[number][]" type="text" value="<?=$v['number']?>" placeholder="数量" class="goodsReceipt_commodity03" onblur="price_n(this.value)">
                <span class="goodsReceipt_commodity_span_unit"><input type="text" name="OrderGoods[unit_name][]" style='border: none;' placeholder="" value="<?=$v['unit_name']?>" readonly="true" /></span>
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
        <?= $form->field($model, 'real_pay')->textInput(['onchange'=>"$('#order-discount').val(parseFloat($('#ordergoods-sell_price').val()*$('#ordergoods-number').val()-$('#order-real_pay').val()).toFixed(2))"])->label(false)->hint('<label>元 * 自动计算，允许修改。计算规则：所有商品的单价*数量之和。</label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>优惠:</p>
        <input type="text" id="order-discount" name="Order[discount]" style="border: none;" value="<?=$model->discount?>" readonly="readonly" />&nbsp;元
        <span>自动计算，计算规则：所有商品的单价*数量之和-实收款。</span>
    </div>
    <h4 class="orders-newtade">客户及收款信息</h4>
    <div class="orders-new clearfix">
        <p>客户帐号:</p>
        <?= $form->field($model, 'customer_name')->textInput()->label(false)->hint('<label>* </label>') ?>
        <?= Html::activeHiddenInput($model,'customer_id')?>
    </div>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <lable id="customers-real_name" style="line-height: 32px"></lable>
    </div>
    <div class="orders-new clearfix">
        <p>收货人姓名:</p>
        <label id="c_address" style="margin: 0">
        <select id="order-address_id" name="Order[address_id]" onchange="$('#address-accept_mobile').text($('#order-address_id option:selected').attr('a1'));$('#address-accept_address').text($('#order-address_id option:selected').attr('a2'));$('#address-accept_idcard').text($('#order-address_id option:selected').attr('a3'));$('#address-is_idcard').text($('#order-address_id option:selected').attr('a4'));$('#accept_name').val($('#order-address_id option:selected').attr('a5'));">
        <option a1="" a2="" a3="" a4="" value="">请选择</option>
        <?php
            $addresslist = $query->select('address_id,accept_name,accept_mobile,accept_address,accept_idcard,is_idcard,idcard_url')->from($tablePrefix.'address')->where('')->all();
            foreach($addresslist as $v){
        ?>
        <option a1="<?=$v['accept_mobile']?>" a2="<?=$v['accept_address']?>" a3="<?=$v['accept_idcard']?>" a4="<?=$v['is_idcard']==1?'是':'否'?>" a5="<?=$v['accept_name']?>" value="<?=$v['accept_name']?>" <?php if($v['address_id']==$model->address_id){echo 'selected';};?>><?=$v['accept_name']?></option>
        <?php }?>
        </select>
        </label>
        <label>*</label>
        <span id="orderaddress_id"></span>
    </div>
    <input type="hidden" id="accept_name" name="accept_name" value="" />
    <?php
        if($model->address_id>0){
            $addressinfo = $query->select('accept_mobile,accept_address,accept_idcard,is_idcard,idcard_url')->from($tablePrefix.'address')->where('address_id='.$model->address_id)->one();
        }
    ?>
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
        <input type="text" id="address-idcard_url" name="Address[idcard_url]" placeholder="格式为：http://idcard.vitagou.com/?accept_mobile=收货人电话&accept_name=收货人姓名&order_no=订单编号&shop_name=销售平台。网址后面的参数值要求Base64加密。" value="<?=!empty($addressinfo['idcard_url'])?$addressinfo['idcard_url']:''?>" style=" border:none;" readOnly="true">
    </div>
    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['order/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
<?php ActiveForm::end(); ?>
<?php \frontend\components\JsBlock::begin()?>
<script>

$(function(){

    $('#ordergoods-goods_name').each(function(){
        $(this).focusin(function(){
            var warehouse_id = $("#order-warehouse_id").val();
            $(this).bigAutocomplete({
                width:353,url:'<?=Url::to(['order/index'])?>&action=goods_info&warehouse_id='+warehouse_id,
                callback:function(data){
                    $("input[name='OrderGoods[spec][]']").val(data.result.spec);
                    $("input[name='OrderGoods[unit_name][]']").val(data.result.unit_name);
                    $("input[name='OrderGoods[barode_code][]']").val(data.result.barode_code);
                    $("input[name='OrderGoods[goods_id][]']").val(data.result.goods_id);
                    $("input[name='OrderGoods[brand_id][]']").val(data.result.brand_id);
                    $("input[name='OrderGoods[brand_name][]']").val(data.result.brand_name);
                    $("input[name='OrderGoods[unit_id][]']").val(data.result.unit_id);
                    $("input[name='OrderGoods[unit_name][]']").val(data.result.unit_name);
                }
            });
        });
    });

    $('#ordergoods-batch_num').each(function(){
        $(this).focusin(function(){
            var warehouse_id = $("#order-warehouse_id").val();
            var goods_id = $("#ordergoods-goods_id").val();
            $(this).bigAutocomplete({
                width:192,url:'<?=Url::to(['order/index'])?>&action=f_batch&warehouse_id='+warehouse_id+'&goods_id='+goods_id,
                callback:function(data){
                    $("input[name='OrderGoods[batch_num][]']").val(data.result.batch_num);
                    $("input[name='OrderGoods[stocks_id][]']").val(data.result.stocks_id);
                }
            });
        });
    });

    $('#goodsReceipt_commodity_btn').each(function(){
        $(this).click(function(){
            var html = '';
            html += '<div class="goodsReceipt_commodity">';
            html += '<input type="hidden" id="ordergoods-id" name="OrderGoods[id][]" value="">';
            html += '<input type="hidden" id="ordergoods-order_id" name="OrderGoods[order_id][]" value="">';
            html += '<input type="hidden" id="ordergoods-goods_id" name="OrderGoods[goods_id][]" value="">';
            html += '<input type="hidden" id="ordergoods-brand_id" name="OrderGoods[brand_id][]" value="">';
            html += '<input type="hidden" id="ordergoods-unit_id" name="OrderGoods[unit_id][]" value="">';
            html += '<input type="text" id="ordergoods-goods_name" name="OrderGoods[goods_name][]" placeholder="商品中英文名称" class="goodsReceipt_commodity01" value="">'
            html += '<span class="goodsReceipt_commodity_span"><input type="text" name="OrderGoods[spec][]" style="border: none;" placeholder="规格" readonly="true" value=""></span>';
            html += '<span class="goodsReceipt_commodity_span"><input type="text" name="OrderGoods[brand_name][]" placeholder="品牌" style="border: none;" readonly="true" value=""></span>';
            html += '<input id="ordergoods-batch_num" name="OrderGoods[batch_num][]" type="text" placeholder="采购批号" onblur="this.value" class="goodsReceipt_commodity02" value="">';
            html += '<input type="hidden" id="ordergoods-stocks_id" name="OrderGoods[stocks_id][]" value="">';
            html += '<input id="ordergoods-sell_price" name="OrderGoods[sell_price][]" type="text" value="" placeholder="单价" onblur="this.value" class="goodsReceipt_commodity03">';
            html += '<span>元</span>';
            html += '<input id="ordergoods-number" name="OrderGoods[number][]" type="text" placeholder="数量" class="goodsReceipt_commodity03" value="">';
            html += '<span class="goodsReceipt_commodity_span_unit"><input type="text" name="OrderGoods[unit_name][]" style="border: none;" placeholder="" value="" readonly="true" value="" /></span>';
            html += '<label class="goodsReceipt_label" onclick="$(this).parent().remove();">-</label>';
            html += '</div>';
            $("#goodsReceipt_commoditybox").append(html);

            $("input[name='OrderGoods[goods_name][]']").each(function(){
                $(this).bigAutocomplete({
                    width:353,data:[
                        <?php foreach($good_list as $v){?>
                        {title:"<?=$v['name']?>",result:{brand_name:"<?=$v['brand_name']?>",spec:"<?=$v['spec']?>",unit_name:"<?=$v['unit_name']?>",barode_code:"<?=$v['barode_code']?>",goods_id:"<?=$v['goods_id']?>",brand_id:"<?=$v['brand_id']?>",brand_name:"<?=$v['brand_name']?>",unit_id:"<?=$v['unit_id']?>"}},
                        <?php }?>
                    ],
                    callback:function(data){

                        $("input[name='OrderGoods[spec][]']").val(data.result.spec);
                        $("input[name='OrderGoods[unit_name][]']").val(data.result.unit_name);
                        $("input[name='OrderGoods[barode_code][]']").val(data.result.barode_code);
                        $("input[name='OrderGoods[goods_id][]']").val(data.result.goods_id);
                        $("input[name='OrderGoods[brand_id][]']").val(data.result.brand_id);
                        $("input[name='OrderGoods[brand_name][]']").val(data.result.brand_name);
                        $("input[name='OrderGoods[unit_id][]']").val(data.result.unit_id);
                        $("input[name='OrderGoods[unit_name][]']").val(data.result.unit_name);
                    }
                });
            });

            $("input[name='Moving[batch_num]']").focusin(function(){
                $("input[name='OrderGoods[batch_num][]']").each(function(){
                    $(this).bigAutocomplete({
                        width:192,data:[
                            <?php foreach($stocks_list as $v){?>
                            {title:"<?=$v['batch_num'].'(库存'.$v['stock_num'].$v['unit_name'].')'?>",result:{batch_num:"<?=$v['batch_num']?>",stocks_id:"<?=$v['stocks_id']?>"}},
                            <?php }?>
                        ],
                        callback:function(data){
                            $("input[name='OrderGoods[batch_num][]']").val(data.result.batch_num);
                            $("input[name='OrderGoods[stocks_id][]']").val(data.result.stocks_id);
                        }
                    });
                });
            });

            //失去焦点计算总金额
            $(".goodsReceipt_commodity03").blur(function () {
                var texts = $(".goodsReceipt_commodity03"),
                    firstValue = null,
                    secondValue = null,
                    sum = 0;

                console.info(texts);

                for (var i = 0, len = texts.length; i < len; i = i + 2) {
                    firstValue = parseInt($(texts[i]).val() || 0);//"||"表示如果前面的值为空，就赋后面的值
                    secondValue = parseInt($(texts[i + 1]).val() || 0);

                    sum += firstValue * secondValue;
                }

                $("#order-real_pay").val(sum);
            });

        })
    });

    $("input[name='Order[customer_name]']").each(function(){
        $(this).bigAutocomplete({
            width:192,data:[
                <?php foreach($customers_list as $v){?>
                {title:"<?=$v['username']?>",result:{customers_id:"<?=$v['customers_id']?>",real_name:"<?=$v['real_name']?>"}},
                <?php }?>
            ],
            callback:function(data){
                $("input[name='Order[customer_id]']").val(data.result.customers_id);
                $("#customers-real_name").text(data.result.real_name);

                //根据帐号调整收货人信息
                $.post("<?=Url::to(['order/index'])?>",{action:'address_info',customers_id:data.result.customers_id},function(result){

                    var html = '';
                    html += '<select id="order-address_id" name="Order[address_id]" onchange=\'$("#address-accept_mobile").text($("#order-address_id option:selected").attr("a1"));$("#address-accept_address").text($("#order-address_id option:selected").attr("a2"));$("#address-accept_idcard").text($("#order-address_id option:selected").attr("a3"));$("#address-is_idcard").text($("#order-address_id option:selected").attr("a4"));$("#accept_name").val($("#order-address_id option:selected").attr("a5"));\'>';
                    html += '<option a1="" a2="" a3="" a4="" value="">请选择</option>';
                    for(var i=0; i<result.length; i++){
                        html += '<option a1='+result[i].accept_mobile+' a2='+result[i].accept_address+' a3='+result[i].accept_idcard+' a4='+result[i].is_idcard+' a5='+result[i].accept_name+' value='+result[i].address_id+'>'+result[i].accept_name+'</option>';
                    }
                    html += '</select>';
                    $("#c_address").html(html);
                },"json");
            }
        });
    });

    //失去焦点计算总金额
    $(".goodsReceipt_commodity03").blur(function () {
        var texts = $(".goodsReceipt_commodity03"),
            firstValue = null,
            secondValue = null,
            sum = 0;

        console.info(texts);

        for (var i = 0, len = texts.length; i < len; i = i + 2) {
            firstValue = parseInt($(texts[i]).val() || 0);//"||"表示如果前面的值为空，就赋后面的值
            secondValue = parseInt($(texts[i + 1]).val() || 0);

            sum += firstValue * secondValue;
        }

        $("#order-real_pay").val(sum);
    });

    //验证收货人姓名
    $("#order-address_id").blur(function(){
        if($.trim($(this).val()) == ''){
            $("#orderaddress_id").html('<label class="red">收货人姓名不能为空</label>');
            return false;
        }else{
            $("#orderaddress_id").text('');
            return true;
        }
    });

    $(".orders-edbut").click(function(){
        if($.trim($('#order-address_id').val()) == ''){
            $("#orderaddress_id").html('<label class="red">收货人姓名不能为空</label>');
            return false;
        }
    });

});

    //生成身份证上传地址
    $("#cardUrl").click(function(){
        $.post("<?=Url::to(['order/index'])?>",{action:'cardUrl',accept_mobile:$('#address-accept_mobile').text(),accept_name:$('#accept_name').val(),order_no:$('#order-order_no').val(),shop_name:$('#order-shop_name').val()},function(result){
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