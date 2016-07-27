<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;
use frontend\models\PurchaseGoods;

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
}else{
    $s_store_id = '';
}

$query = new \yii\db\Query();
//获取仓库
$warehose = $query->select('warehouse_id,name')->from(Yii::$app->getDb()->tablePrefix.'warehouse')->where('status=1'.$s_store_id)->all();
$warehose_row = array();
if(!empty($warehose)){
    $warehose_row[''] = '请选择';
    foreach($warehose as $value){
        $warehose_row[$value['warehouse_id']] = $value['name'];
    }
}

//获取负责人
$user = \frontend\components\Search::SearchUser();
$user_row = array();
if(!empty($user)){
    $user_row[''] = '请选择';
    foreach($user as $value){
        if($value['type']==2){
            $user_row[$value['user_id']] = $value['real_name'];
        }
    }
}

$good_list = \frontend\components\Search::SearchGoods();
$supplier_list = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'suppliers')->where('status=1'.$s_store_id)->orderBy(['sort'=>SORT_ASC,'create_time'=>SORT_DESC])->all();
?>
<?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord){?>
        <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家ID-->
        <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
        <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?><!--创建时间-->
        <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
        <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['real_name']])?>
    <?php }?>

    <div class="orders-new clearfix">
        <p>入库仓库:</p>
        <?= $form->field($model, 'warehouse_id')->dropDownList($warehose_row,['onchange'=>"$('#purchase-warehouse_name').val($('#purchase-warehouse_id option:selected').text())"])->label(false)->hint('<label>* </label>') ?>
        <?= Html::activeHiddenInput($model,'warehouse_name')?>
    </div>
    <h4 class="orders-newtade">商品信息</h4>
    <div class="orders-new clearfix">
        <p>商品中英文名称:</p>
        <?= $form->field($model_pg, 'goods_name')->textInput()->label(false)->hint('<label>* 在此输入会自动过滤商品。选择商品后下面的商品信息会自动填写。</label>') ?>
        <input type="hidden" id="purchasegoods-goods_id" name="PurchaseGoods[goods_id]" value="<?=$model_pg->goods_id?>" />
        <span id="g_name" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>规格:</p>
        <input type="text" id="purchasegoods-spec" name="PurchaseGoods[spec]" style="border: none; background:#fff;" value="<?=$model_pg->spec?>" readonly="true" />
    </div>
    <div class="orders-new clearfix">
        <p>品牌:</p>
        <input type="text" id="purchasegoods-brand_name" name="PurchaseGoods[brand_name]" style="border: none; background:#fff;" value="<?=$model_pg->brand_name?>" readonly="true" />
        <input type="hidden" id="purchasegoods-brand_id" name="PurchaseGoods[brand_id]" value="<?=$model_pg->brand_id?>" />
    </div>
    <div class="orders-new clearfix">
        <p>单位:</p>
        <input type="text" id="purchasegoods-unit_name" name="PurchaseGoods[unit_name]" style="border: none; background:#fff;" value="<?=$model_pg->unit_name?>" readonly="true" />
        <input type="hidden" id="purchasegoods-unit_id" name="PurchaseGoods[unit_id]" value="<?=$model_pg->unit_id?>" />
    </div>
    <div class="orders-new clearfix">
        <p>条形码:</p>
        <input type="text" id="purchasegoods-barode_code" name="PurchaseGoods[barode_code]" style='border: none; background:#fff;' value="<?=$model_pg->barode_code?>" readonly="true" />
    </div>

    <h4 class="orders-newtade">采购信息</h4>
    <div class="orders-new clearfix">
        <p>采购单价:</p>
        <?= $form->field($model_pg, 'buy_price')->textInput(['onchange'=>"$('#purchase-totle_price').val(parseFloat($('#purchasegoods-buy_price').val()*$('#purchasegoods-number').val()).toFixed(2))"])->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>采购数量:</p>
        <?= $form->field($model_pg, 'number')->textInput(['onchange'=>"$('#purchase-totle_price').val(parseFloat($('#purchasegoods-buy_price').val()*$('#purchasegoods-number').val()).toFixed(2))"])->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>总价:</p>
        <?= $form->field($model, 'totle_price')->textInput()->label(false)->hint('<label>* 自动计算，允许修改。</label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>失效日期:</p>
        <?= $form->field($model, 'invalid_time')->textInput(['id'=>'ex-date','class'=>'laydate-icon','value'=>$model->invalid_time>0?date('Y-m-d', $model->invalid_time):'','onblur'=>"SetHfdValue3(this.value)"])->label(false)->hint('<label>* </label>') ?>
        <span id="invalid_time" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>批号:</p>
        <?= $form->field($model, 'batch_num')->textInput()->label(false)->hint('<label>* 根据失效日期自动计算，规则为失效日期组成的八位数，允许修改</label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>采购日期:</p>
        <?= $form->field($model, 'buy_time')->textInput(['id'=>'pur-date','class'=>'laydate-icon','value'=>$model->buy_time>0?date('Y-m-d', $model->buy_time):date('Y-m-d', time())])->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>供应商:</p>
        <?= $form->field($model_pg, 'supplier_name')->textInput()->label(false)->hint('<label>* </label>') ?>
        <input type="hidden" id="purchasegoods-supplier_id" name="PurchaseGoods[supplier_id]" value="<?=$model_pg['supplier_id']?>" />
        <span id="s_name" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p class="orders-newt1">发票和付款情况:</p>
        <?= Html::activeTextarea($model,'invoice_and_pay_sate',['class' => 'orders-newt2'])?>
    </div>
    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-new clearfix">
        <p>负责人:</p>
        <?= $form->field($model, 'principal_id')->dropDownList($user_row,['onchange'=>"$('#purchase-principal_name').val($('#purchase-principal_id option:selected').text())"])->label(false)->hint('<label>* 默认为当前登录人姓名，可选值为系统中的普通管理员姓名。</label>') ?>
        <?= Html::activeHiddenInput($model,'principal_name')?>
    </div>
    <div class="orders-new clearfix">
        <p class="orders-newt1">备注说明:</p>
        <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['purchase/index'])?>">
            <span class="orders-newbut2">返回</span>
        </a>
    </div>
<?php ActiveForm::end(); ?>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            //选择商品中英文名称
            $("input[name='PurchaseGoods[goods_name]']").bigAutocomplete({
                width:510,
                data:[
                    <?php foreach($good_list as $v){?>
                    {title:"<?=$v['name']?>",result:{goods_name:"<?=$v['name']?>",brand_name:"<?=$v['brand_name']?>",spec:"<?=$v['spec']?>",unit_name:"<?=$v['unit_name']?>",barode_code:"<?=$v['barode_code']?>",goods_id:"<?=$v['goods_id']?>",brand_id:"<?=$v['brand_id']?>",unit_id:"<?=$v['unit_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){

                    $("#g_name").text('');
                    $('.orders-edbut').attr('disabled',false);

                    $("#purchasegoods-goods_name").val(data.result.goods_name);
                    $("#purchasegoods-brand_name").val(data.result.brand_name);
                    $("#purchasegoods-spec").val(data.result.spec);
                    $("#purchasegoods-unit_name").val(data.result.unit_name);
                    $("#purchasegoods-barode_code").val(data.result.barode_code);
                    $("#purchasegoods-goods_id").val(data.result.goods_id);
                    $("#purchasegoods-brand_id").val(data.result.brand_id);
                    $("#purchasegoods-unit_id").val(data.result.unit_id);
                }
            });

            $("input[name='PurchaseGoods[supplier_name]']").bigAutocomplete({
                width:510,
                data:[
                    <?php foreach($supplier_list as $v){?>
                    {title:"<?=$v['name']?>",result:{suppliers_id:"<?=$v['suppliers_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){

                    $("#s_name").text('');
                    $('.orders-edbut').attr('disabled',false);

                    $("#purchasegoods-supplier_id").val(data.result.suppliers_id);
                }
            });

        });

        //验证商品中英文名称是否存在
        $("input[name='PurchaseGoods[goods_name]']").keyup(function(){
            var goods_name = $("input[name='PurchaseGoods[goods_name]']").val();
            if(goods_name!=''){
                $.post("<?=Url::to(['purchase/index'])?>",{action:'gname',goods_name:goods_name},function(result){
                    if(result==''){
                        $("#g_name").text('商品中英文名称不存在');
                        $(".field-purchasegoods-goods_name>.hint-block").html("");
                        $('.orders-edbut').attr('disabled',true);
                        return false;
                    }else{
                        $("#purchasegoods-goods_name").val(result.name);
                        $("#purchasegoods-brand_name").val(result.brand_name);
                        $("#purchasegoods-spec").val(result.spec);
                        $("#purchasegoods-unit_name").val(result.unit_name);
                        $("#purchasegoods-barode_code").val(result.barode_code);
                        $("#purchasegoods-goods_id").val(result.goods_id);
                        $("#purchasegoods-brand_id").val(result.brand_id);
                        $("#purchasegoods-unit_id").val(result.unit_id);
                        $("#g_name").text('');
                        $('.orders-edbut').attr('disabled',false);
                    }
                },'json');
            }else{
                $("#purchasegoods-goods_name").val('');
                $("#purchasegoods-brand_name").val('');
                $("#purchasegoods-spec").val('');
                $("#purchasegoods-unit_name").val('');
                $("#purchasegoods-barode_code").val('');
                $("#purchasegoods-goods_id").val('');
                $("#purchasegoods-brand_id").val('');
                $("#purchasegoods-unit_id").val('');
            }

        });

        //验证供应商是否存在
        $("input[name='PurchaseGoods[supplier_name]']").keyup(function(){
            var supplier_name = $("input[name='PurchaseGoods[supplier_name]']").val();
            if(supplier_name!=''){
                $.post("<?=Url::to(['purchase/index'])?>",{action:'sname',supplier_name:supplier_name},function(result){
                    if(result==''){
                        $("#s_name").text('供应商名称不存在');
                        $('.orders-edbut').attr('disabled',true);
                        return false;
                    }else{
                        $("#purchasegoods-supplier_id").val(result.suppliers_id);
                        $("#s_name").text('');
                        $('.orders-edbut').attr('disabled',false);
                    }
                },'json');
            }

        });

    </script>
    <script type="text/javascript">
        laydate({ elem: '#ex-date', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
        event: 'focus',
        // 响应事件。如果没有传入event，则按照默认的click
        choose: function (datas) { //选择日期完毕的回调
            var timestamp = Date.parse(new Date());
            timestamp = timestamp / 1000;//当前时间
            var timestamp2 = Date.parse(new Date(datas));
            timestamp2 = timestamp2 / 1000;//失效日期
            if(timestamp2<timestamp){
                $("#invalid_time").text('失效日期必须大于当前时间');
                $('.orders-edbut').attr('disabled',true);
                return false;
            }else{
                $("#invalid_time").text('');
                $('.orders-edbut').attr('disabled',false);
            }
            SetHfdValue3(datas);
        }
        });
        function SetHfdValue3(datas) {//将选择的值赋值给HiddenField
            $("#purchase-batch_num").val(datas.replace(/-/g, ''));
        }
    </script>

<?php \frontend\components\JsBlock::end()?>