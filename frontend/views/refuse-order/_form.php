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
$orders = \frontend\components\Search::SearchOrder();
$goods = \frontend\components\Search::SearchGoods();
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
        <p>销售平台:</p>
        <?= $form->field($model, 'shop_id')->dropDownList($shop_row,['onchange'=>"$('#refuseorder-shop_name').val($('#refuseorder-shop_id option:selected').text())"])->label(false)->hint('<label>* 请选择销售平台。</label>') ?>
        <?= Html::activeHiddenInput($model,'shop_name')?>
    </div>
    <div class="orders-new clearfix">
        <p>订单编号:</p>
        <?= $form->field($model, 'order_no')->textInput()->label(false)->hint('<label>* 请选择该销售平台下的订单编号,选择后自动回显下面的相关信息。</label>') ?>
        <?= Html::activeHiddenInput($model,'order_id')?>
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
    <div class="orders-new clearfix">
        <p>客户帐号:</p>
        <?= Html::activeTextInput($model,'customer_name',['style'=>'border:none','readonly'=>'true'])?>
        <?= Html::activeHiddenInput($model,'customer_id')?>
    </div>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <lable id="real_name" style=" line-height: 34px;"></lable>
    </div>
    <h4 class="orders-newtade">退货相关信息*</h4>
    <div class="orders-new clearfix">
        <p>商品及赠品:</p>
        <div class="goodsReceipt" id="goodsReceipt_commoditybox">
    <?php if(!empty($rog_model)){?>
        <?php foreach($rog_model as $v){?>
        <div class="goodsReceipt_commodity" id="goodsReceipt_commoditybox">
            <input type="hidden" name="RefuseOrderGoods[id][]" value="<?=$v['id']?>" />
            <input type="hidden" name="RefuseOrderGoods[goods_id][]" value="<?=$v['goods_id']?>" />
            <input type="hidden" name="RefuseOrderGoods[brand_id][]" value="<?=$v['brand_id']?>" />
            <input type="hidden" name="RefuseOrderGoods[unit_id][]" value="<?=$v['unit_id']?>" />
            <input type="text" id="refuseordergoods-name" name="RefuseOrderGoods[goods_name][]" style="border: none;" placeholder="商品中英文名称" readonly="true" class="goodsReceipt_commodity01" value="<?=$v['goods_name']?>">
            <input type="hidden" name="RefuseOrderGoods[spec][]" value="<?=$v['spec']?>">
            <input type="hidden" name="RefuseOrderGoods[brand_name][]" value="<?=$v['brand_name']?>">
            <span class="goodsReceipt_commodity_span"><input type="text" name="RefuseOrderGoods[spec][]" style="border: none;" placeholder="规格" value="<?=$v['spec']?>" readonly="true"></span>
            <span class="goodsReceipt_commodity_span"><input type="text" name="RefuseOrderGoods[brand_name][]" placeholder="品牌" style="border: none;" value="<?=$v['brand_name']?>" readonly="true"></span>
            <input id="refuseordergoods-batch_num" name="RefuseOrderGoods[batch_num][]" type="text" style="border: none;" placeholder="采购批号" readonly="true" class="goodsReceipt_commodity02" value="<?=$v['batch_num']?>">
            <input id="refuseordergoods-sell_price" name="RefuseOrderGoods[sell_price][]" type="text" style="border: none;" placeholder="单价" readonly="true" value="<?=$v['sell_price'].'&nbsp;&nbsp;元'?>" class="goodsReceipt_commodity03">
            <input id="refuseordergoods-number" name="RefuseOrderGoods[number][]" type="text" value="<?=$v['number']?>" placeholder="数量" class="goodsReceipt_commodity03">
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
    </div>
    <div class="orders-new clearfix">
        <p>退款日期:</p>
        <?= $form->field($model, 'refuse_time')->textInput(['id'=>'pur-date','class'=>'laydate-icon','value'=>$model->refuse_time])->label(false)->hint('<label>* </label>') ?>
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
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
<?php ActiveForm::end(); ?>

<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $("input[name='RefuseOrderGoods[goods_name][]']").each(function(){
                $(this).bigAutocomplete({
                    width:353,data:[
                        <?php foreach($good_list as $v){?>
                        {title:"<?=$v['name']?>",result:{brand_name:"<?=$v['brand_name']?>",spec:"<?=$v['spec']?>",unit_name:"<?=$v['unit_name']?>",barode_code:"<?=$v['barode_code']?>",goods_id:"<?=$v['goods_id']?>",brand_id:"<?=$v['brand_id']?>",brand_name:"<?=$v['brand_name']?>",unit_id:"<?=$v['unit_id']?>"}},
                        <?php }?>
                    ],
                    callback:function(data){
                        $("input[name='RefuseOrderGoods[spec][]']").val(data.result.spec);
                        $("input[name='RefuseOrderGoods[unit_name][]']").val(data.result.unit_name);
                        $("input[name='RefuseOrderGoods[barode_code][]']").val(data.result.barode_code);
                        $("input[name='RefuseOrderGoods[goods_id][]']").val(data.result.goods_id);
                        $("input[name='RefuseOrderGoods[brand_id][]']").val(data.result.brand_id);
                        $("input[name='RefuseOrderGoods[brand_name][]']").val(data.result.brand_name);
                        $("input[name='RefuseOrderGoods[unit_id][]']").val(data.result.unit_id);
                        $("input[name='RefuseOrderGoods[unit_name][]']").val(data.result.unit_name);
                    }
                });
            });

            $("input[name='RefuseOrderGoods[batch_num][]']").each(function(){
                $(this).bigAutocomplete({
                    width:220,data:[
                        <?php foreach($stocks_list as $v){?>
                        {title:"<?=$v['batch_num'].'(库存'.$v['stock_num'].$v['unit_name'].')'?>",result:{batch_num:"<?=$v['batch_num']?>"}},
                        <?php }?>
                    ],
                    callback:function(data){
                        $("input[name='RefuseOrderGoods[batch_num][]']").val(data.result.batch_num);
                    }
                });
            });

            $("input[name='RefuseOrder[order_no]']").bigAutocomplete({
                width:300,data:[
                    <?php
                    foreach($orders as $v){
                        $real_name = $query->select('real_name')->from($tablePrefix.'customers')->where('customers_id='.$v['customer_id'])->one();//客服真实姓名

                    ?>
                    {title:"<?=$v['order_no']?>",result:{order_id:"<?=$v['order_id']?>",real_pay:"<?=$v['real_pay']?>",sale_time:"<?=date('Y-m-d', $v['sale_time'])?>",customer_id:"<?=$v['customer_id']?>",customer_name:"<?=$v['customer_name']?>",real_name:"<?=$real_name['real_name']?>",order_goods:""}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='RefuseOrder[order_id]']").val(data.result.order_id);
                    $("input[name='RefuseOrder[refuse_real_pay]']").val(data.result.real_pay);
                    $("input[name='RefuseOrder[sale_time]']").val(data.result.sale_time);
                    $("input[name='RefuseOrder[customer_id]']").val(data.result.customer_id);
                    $("input[name='RefuseOrder[customer_name]']").val(data.result.customer_name);
                    $("#real_name").text(data.result.real_name);

                    $.post("<?=Url::to(['refuse-order/index'])?>",{action:'goods',order_id:data.result.order_id},function(result){
                        var html = '';
                        for(var i=0; i<result.length; i++){
                            html += '<div class="goodsReceipt_commodity" id="goodsReceipt_commoditybox">';
                            html += '<input type="hidden" name="RefuseOrderGoods[id][]" value="'+result[i].id+'" />';
                            html += '<input type="hidden" name="RefuseOrderGoods[goods_id][]" value="'+result[i].goods_id+'" />';
                            html += '<input type="hidden" name="RefuseOrderGoods[brand_id][]" value="'+result[i].brand_id+'" />';
                            html += '<input type="hidden" name="RefuseOrderGoods[unit_id][]" value="'+result[i].unit_id+'" />';
                            html += '<input type="text" id="refuseordergoods-name" name="RefuseOrderGoods[goods_name][]" class="goodsReceipt_commodity01" style="border: none;" placeholder="商品中英文名称" readonly="true" value='+result[i].goods_name+'>';
                            html += '<input type="hidden" name="RefuseOrderGoods[spec][]" value="'+result[i].spec+'">';
                            html += '<input type="hidden" name="RefuseOrderGoods[brand_name][]" value="'+result[i].brand_name+'">';
                            html += '<span class="goodsReceipt_commodity_span"><input type="text" name="RefuseOrderGoods[spec][]" style="border: none;" placeholder="规格" readonly="true" value='+result[i].spec+'></span>';
                            html += '<span class="goodsReceipt_commodity_span"><input type="text" name="RefuseOrderGoods[brand_name][]" placeholder="品牌" style="border: none;" readonly="true" value='+result[i].brand_name+'></span>';
                            html += '<input id="refuseordergoods-batch_num" name="RefuseOrderGoods[batch_num][]" type="text" class="goodsReceipt_commodity02" style="border: none;" placeholder="采购批号" readonly="true" value="'+result[i].batch_num+'">';
                            html += '<input id="refuseordergoods-sell_price" name="RefuseOrderGoods[sell_price][]" type="text" style="border: none;" placeholder="单价" readonly="true" class="goodsReceipt_commodity03" value="'+result[i].sell_price+'&nbsp;&nbsp;元">';
                            html += '<input id="refuseordergoods-number" name="RefuseOrderGoods[number][]" type="text" value="'+result[i].number+'" placeholder="数量" class="goodsReceipt_commodity03">';
                            html += '<span class="goodsReceipt_commodity_span_unit"><input type="text" name="RefuseOrderGoods[unit_name][]" style="border: none;" placeholder="" readonly="true" value='+result[i].unit_name+'></span>';
                            html += '<label class="goodsReceipt_label" onclick="$(this).parent().remove();">-</label>';
                            html += '</div>';

                            $(".goodsReceipt").html(html);
                        }

                    },"json");

                }
            });

            $('.goodsReceipt_label').each(function(){
                $(this).click(function(){
                    $(this).parent().remove();
                })
            });

        });
    </script>
<?php \frontend\components\JsBlock::end()?>