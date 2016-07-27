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
$goods = \frontend\components\Search::SearchGoods();
?>

<?php $form = ActiveForm::begin(); ?>
<?php if($model->isNewRecord){?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家ID-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?><!--创建时间-->
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['real_name']])?>
<?php }?>
    <h4 class="orders-newtade">订单基本信息</h4>
    <div class="orders-new clearfix">
        <p>销售平台:</p>
        <?= $form->field($model, 'shop_id')->dropDownList($shop_row,['onchange'=>"$('#refuseorder-shop_name').val($('#refuseorder-shop_id option:selected').text())"])->label(false)->hint('<label>* 请选择销售平台。</label>') ?>
        <?= Html::activeHiddenInput($model,'shop_name')?>
    </div>
    <div class="orders-new clearfix">
        <p>订单编号:</p>
        <?= $form->field($model, 'order_no')->textInput(['autocomplete'=>'off'])->label(false)->hint('<label>* 请选择该销售平台下的订单编号,选择后自动回显下面的相关信息。</label>') ?>
        <?= Html::activeHiddenInput($model,'order_id')?>
        <span id="s_name" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>实收款:</p>
        <?= Html::activeTextInput($model,'refuse_real_pay',['style'=>'border:none','readonly'=>'true'])?>
    </div>
    <div class="orders-new clearfix">
        <p>销售日期:</p>
        <?= Html::activeTextInput($model,'sale_time',['style'=>'border:none','readonly'=>'true','value'=>$model->sale_time>0?date('Y-m-d',$model->sale_time):''])?>
    </div>
    <h4 class="orders-newtade">客户信息</h4>
    <?php
    if($model->customer_id>0){
        $real_name = (new \yii\db\Query())->select('username,real_name')->from($tablePrefix.'customers')->where('customers_id='.$model->customer_id)->one();
    }
    ?>
    <div class="orders-new clearfix">
        <p>客户帐号:</p>
        <?= Html::activeTextInput($model,'customer_name',['style'=>'border:none','readonly'=>'true','value'=>!empty($real_name)?$real_name['username']:''])?>
        <?= Html::activeHiddenInput($model,'customer_id')?>
    </div>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <lable id="real_name" style=" line-height: 34px;"><?=!empty($real_name)?$real_name['real_name']:''?></lable>
    </div>
    <h4 class="orders-newtade">退货相关信息*<span id="og-error"></span></h4>
    <div class="orders-new clearfix">
        <p>商品及赠品:</p>
        <div class="goodsReceipt" id="goodsReceipt_commoditybox">
    <?php if(!empty($rog_model)){?>
        <?php
        foreach($rog_model as $v){
            if(!empty($v['stocks_id'])){
                $wstock['stocks_id'] =  $v['stocks_id'];
                $stock_num = $query->select('stock_num')->from($tablePrefix.'stocks')->where($wstock)->one();
            }
            if(!empty($v['goods_id'])){
                $goods_row = $query->select('*')->from($tablePrefix.'goods')->where('goods_id='.$v['goods_id'])->one();
            }
        ?>
        <div class="goodsReceipt_commodity" id="goodsReceipt_commoditybox">
            <input type="hidden" name="RefuseOrderGoods[id][]" value="<?=$v['id']?>" />
            <input type="hidden" name="RefuseOrderGoods[goods_id][]" value="<?=$v['goods_id']?>" />
            <input type="hidden" name="RefuseOrderGoods[brand_id][]" value="<?=$v['brand_id']?>" />
            <input type="hidden" name="RefuseOrderGoods[unit_id][]" value="<?=$v['unit_id']?>" />
            <input type="hidden" name="RefuseOrderGoods[stocks_id][]" value="<?=$v['stocks_id']?>" />
            <input type="text" id="refuseordergoods-name" name="RefuseOrderGoods[goods_name][]" style="border: none;" placeholder="商品中英文名称" readonly="true" class="goodsReceipt_commodity01" value="<?=!empty($goods_row['name'])?$goods_row['name']:''?>">
            <span class="goodsReceipt_commodity_span" style="width: 7%"><input type="text" name="RefuseOrderGoods[spec][]" style="border: none;" placeholder="规格" value="<?=!empty($goods_row['spec'])?$goods_row['spec']:''?>" readonly="true"></span>
            <span class="goodsReceipt_commodity_span" style="width: 13%"><input type="text" name="RefuseOrderGoods[brand_name][]" placeholder="品牌" style="border: none;" value="<?=!empty($goods_row['brand_name'])?$goods_row['brand_name']:''?>" readonly="true"></span>
            <input id="refuseordergoods-batch_num" name="RefuseOrderGoods[batch_num][]" type="text" style="border: none;" placeholder="采购批号" readonly="true" class="goodsReceipt_commodity02" value="<?=$v['batch_num']?>">
            <input id="refuseordergoods-sell_price" name="RefuseOrderGoods[sell_price][]" type="text" style="border: none;" placeholder="单价" readonly="true" value="<?=$v['sell_price']?>" class="goodsReceipt_commodity03">
            <input id="refuseordergoods-number" name="RefuseOrderGoods[number][]" type="text" class="goodsReceipt_commodity03 a2" value="<?=$v['number']?>" rel="<?=$v['number']?>" placeholder="数量">
            <span class="goodsReceipt_commodity_span_unit"><input type="text" name="RefuseOrderGoods[unit_name][]" style='border: none;' placeholder="" value="<?=$v['unit_name']?>" readonly="true" /></span>
            <label class="goodsReceipt_label">-</label>
        </div>
        <?php }?>
    <?php }?>
    </div>
    </div>

    <div class="orders-new clearfix">
        <p>退款金额:</p>
        <?= $form->field($model, 'refuse_amount')->textInput()->label(false)->hint('<label>元 * 请输入退款金额。</label>') ?>
        <span id="r_amount" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>退款日期:</p>
        <?= $form->field($model, 'refuse_time')->textInput(['id'=>'pur-date','class'=>'laydate-icon','value'=>$model->refuse_time>0?date('Y-m-d', $model->refuse_time):date('Y-m-d', time())])->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>退款原因及说明:</p>
        <?= Html::activeTextarea($model,'reason',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-new clearfix">
        <p>仓库:</p>
        <?= $form->field($model, 'warehouse_id')->dropDownList($warehose_row,['onchange'=>"$('#refuseorder-warehouse_name').val($('#refuseorder-warehouse_id option:selected').text())"])->label(false)->hint('<label>* 请选择退货订单入库仓库。</label>') ?>
        <?= Html::activeHiddenInput($model,'warehouse_name')?>
    </div>
    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['refuse-order/index'])?>">
            <span class="orders-newbut2">返回</span>
        </a>
    </div>
<?php ActiveForm::end(); ?>

<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){

            $("input[name='RefuseOrder[order_no]']").bind("focusin keyup",function(){

                var shop_id = $("select[name='RefuseOrder[shop_id]']").val();
                if(shop_id!=''){
                    $("input[name='RefuseOrder[order_no]']").bigAutocomplete({
                        width:300,
                        url:'<?=Url::to(['refuse-order/index'])?>&action=f_order_no&shop_id='+shop_id+'&order_no='+$("input[name='RefuseOrder[order_no]']").val(),
                        callback:function(data){
                            $("input[name='RefuseOrder[order_no]']").val(data.title);
                            $("input[name='RefuseOrder[order_id]']").val(data.result.order_id);
                            $("input[name='RefuseOrder[refuse_real_pay]']").val(data.result.real_pay);
                            $("input[name='RefuseOrder[sale_time]']").val(data.result.sale_time);
                            $("input[name='RefuseOrder[customer_id]']").val(data.result.customer_id);
                            $("input[name='RefuseOrder[customer_name]']").val(data.result.customer_name);
                            $("#real_name").text(data.result.real_name);
                            $("input[name='RefuseOrder[refuse_amount]']").val(data.result.real_pay);
                            $.post("<?=Url::to(['refuse-order/index'])?>",{action:'goods',order_id:data.result.order_id},function(result){
                                $("#s_name").text('');
                                $('.orders-edbut').attr('disabled',false);
                                var html = '';
                                for(var i=0; i<result.length; i++){
                                    html += '<div class="goodsReceipt_commodity" id="goodsReceipt_commoditybox">';
                                    html += '<input type="hidden" name="RefuseOrderGoods[id][]" value="'+result[i].id+'" />';
                                    html += '<input type="hidden" name="RefuseOrderGoods[goods_id][]" value="'+result[i].goods_id+'" />';
                                    html += '<input type="hidden" name="RefuseOrderGoods[brand_id][]" value="'+result[i].brand_id+'" />';
                                    html += '<input type="hidden" name="RefuseOrderGoods[unit_id][]" value="'+result[i].unit_id+'" />';
                                    html += '<input type="hidden" name="RefuseOrderGoods[stocks_id][]" value="'+result[i].stocks_id+'" />';
                                    html += '<input type="text" id="refuseordergoods-name" name="RefuseOrderGoods[goods_name][]" class="goodsReceipt_commodity01" style="border: none;" placeholder="商品中英文名称" readonly="true" value='+result[i].goods_name+'>';
                                    html += '<span class="goodsReceipt_commodity_span" style="width: 7%"><input type="text" name="RefuseOrderGoods[spec][]" style="border: none;" placeholder="规格" readonly="true" value='+result[i].spec+'></span>';
                                    html += '<span class="goodsReceipt_commodity_span" style="width: 13%"><input type="text" name="RefuseOrderGoods[brand_name][]" placeholder="品牌" style="border: none;" readonly="true" value='+result[i].brand_name+'></span>';
                                    html += '<input id="refuseordergoods-batch_num" name="RefuseOrderGoods[batch_num][]" type="text" class="goodsReceipt_commodity02" style="border: none;" placeholder="采购批号" readonly="true" value="'+result[i].batch_num+'">';
                                    html += '<input id="refuseordergoods-sell_price" name="RefuseOrderGoods[sell_price][]" type="text" style="border: none;" placeholder="单价" readonly="true" class="goodsReceipt_commodity03" value="'+result[i].sell_price+'">';
                                    html += '<input id="refuseordergoods-number" name="RefuseOrderGoods[number][]" type="text" class="goodsReceipt_commodity03 a2" value="'+result[i].number+'" rel='+result[i].number+' placeholder="数量">';
                                    html += '<span class="goodsReceipt_commodity_span_unit"><input type="text" name="RefuseOrderGoods[unit_name][]" style="border: none;" placeholder="" readonly="true" value='+result[i].unit_name+'></span>';
                                    html += '<label class="goodsReceipt_label">-</label>';
                                    html += '</div>';

                                    $(".goodsReceipt").html(html);
                                }

                            },"json");

                        }
                    });
                }

            });

            $('.goodsReceipt').on('click','.goodsReceipt_label',function(){
                for(var i=0;$('.goodsReceipt_commodity').length > i;i++){
                    if(i > 0){
                        $(this).parent().remove();
                    }
                }
            });

            //验证订单编号是否存在
            $("input[name='RefuseOrder[order_no]']").blur(function(){
                var order_no = $("input[name='RefuseOrder[order_no]']").val();
                if(order_no!=''){
                    $.post("<?=Url::to(['refuse-order/index'])?>",{action:'oorder_no',order_no:order_no},function(result){
                        if(result==0){
                            $("#s_name").text('订单编号不存在');
                            $('.orders-edbut').attr('disabled',true);
                            return false;
                        }else{
                            $("#s_name").text('');
                            $('.orders-edbut').attr('disabled',false);
                        }
                    },'json');
                }else{
                    $("input[name='RefuseOrder[order_no]']").val('');
                    $("input[name='RefuseOrder[order_id]']").val('');
                    $("input[name='RefuseOrder[refuse_real_pay]']").val('');
                    $("input[name='RefuseOrder[sale_time]']").val('');
                    $("input[name='RefuseOrder[customer_id]']").val('');
                    $("input[name='RefuseOrder[customer_name]']").val('');
                    $("#real_name").text('');
                    $("input[name='RefuseOrder[refuse_amount]']").val('');

                    $(".goodsReceipt").html('');
                }

            });

            //验证数量
            $('.goodsReceipt').on('blur','#refuseordergoods-number',function(){
                if($.trim($(this).val()) == ''){
                    $("#og-error").html('<label class="red">数量不能为空</label>');
                    return false;
                }else{
                    if(isNaN($.trim($(this).val())) == true){
                        $("#og-error").html('<label class="red">数量必须为数字</label>');
                        return false;
                    }else{
                        if($.trim($(this).val())<=0 || $.trim($(this).val()) > parseInt($(this).attr('rel'))){
                            $("#og-error").html('<label class="red">退货数量不能小于等于0或大于销售数量</label>');
                            return false;
                        }else{
                            $("#og-error").text('');
                            return true;
                        }

                    }
                }
            });

            //验证退款金额不能大于实收款金额
            $("input[name='RefuseOrder[refuse_amount]']").blur(function(){
                var refuse_real_pay = $("input[name='RefuseOrder[refuse_real_pay]']").val();//实收款
                var refuse_amount = $("input[name='RefuseOrder[refuse_amount]']").val();//退款金额
                if(parseFloat(refuse_amount)<0 || parseFloat(refuse_amount)>refuse_real_pay){
                    $("#r_amount").text('退款金额不能小于0或大于实收款金额');
                    return false;
                }else{
                    $("#r_amount").text('');
                }
            });

            $(".orders-edbut").click(function(){
                for(var i=0;i< $('.goodsReceipt_commodity01').length;i++){
                    var number = $.trim($('.a2').eq(i).val());
                    var order_num = $('.a2').eq(i).attr('rel');
                    if(number == ''){
                        $("#og-error").html('<label class="red">数量不能为空</label>');
                        return false;
                    }
                    if(isNaN(number) == true){
                        $("#og-error").html('<label class="red">数量必须为数字</label>');
                        return false;
                    }
                    if(number<=0 || number>parseInt(order_num)){
                        $("#og-error").html('<label class="red">退货数量不能小于等于0或大于销售数量</label>');
                        return false;
                    }

                }

                var refuse_real_pay = $("input[name='RefuseOrder[refuse_real_pay]']").val();//实收款
                var refuse_amount = $("input[name='RefuseOrder[refuse_amount]']").val();//退款金额
                if(parseFloat(refuse_amount)<0 || parseFloat(refuse_amount)>refuse_real_pay){
                    $("#r_amount").text('退款金额不能小于0或大于实收款金额');
                    return false;
                }

            });

        });
    </script>
<?php \frontend\components\JsBlock::end()?>