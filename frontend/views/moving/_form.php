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
?>
<?php $form = ActiveForm::begin(); ?>
<?php if($model->isNewRecord){?>
    <?= Html::activeHiddenInput($model,'store_id',['value'=>$userinfo['store_id']])?><!--入驻商家ID-->
    <?= Html::activeHiddenInput($model,'store_name',['value'=>$userinfo['store_name']])?>
    <?= Html::activeHiddenInput($model,'create_time',['value'=>time()])?><!--创建时间-->
    <?= Html::activeHiddenInput($model,'add_user_id',['value'=>$userinfo['user_id']])?><!--创建人id-->
    <?= Html::activeHiddenInput($model,'add_user_name',['value'=>$userinfo['real_name']])?>
<?php }?>
    <h4 class="orders-newtade">仓库信息</h4>
    <div class="orders-new clearfix">
        <p>仓库:</p>
        <select id="moving-from_warehouse_id" style="width: 29.6%" name="Moving[from_warehouse_id]" onchange="$('#moving-from_warehouse_name').val($('#moving-from_warehouse_id option:selected').attr('title'))">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option title="<?php echo $v['name'];?>" value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==$model->from_warehouse_id){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select> -> <select id="moving-to_warehouse_id" style="width: 29.6%" name="Moving[to_warehouse_id]" onchange="$('#moving-to_warehouse_name').val($('#moving-to_warehouse_id option:selected').attr('title'))">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option title="<?php echo $v['name'];?>" value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==$model->to_warehouse_id){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select>
        <?= Html::activeHiddenInput($model,'from_warehouse_name')?>
        <?= Html::activeHiddenInput($model,'to_warehouse_name')?>
        <label>*</label>
        <span id="movingwarehouse_id">请选择仓库，前面是调出仓库，后面是调入仓库。</span>
    </div>
    <h4 class="orders-newtade">商品信息</h4>
    <div class="orders-new clearfix">
        <p>商品中英文名称:</p>
        <?=$form->field($model, 'goods_name')->textInput()->label(false)->hint('<label>* 在此输入会自动过滤商品。选择商品后下面的商品信息会自动填写</label>')?>
        <?= Html::activeHiddenInput($model,'goods_id')?>
        <span id="g_name" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>品牌:</p>
        <input type="text" id="moving-brand_name" name="Moving[brand_name]" value="<?=$model->brand_name?>" style="border: none; background: #fff;" readOnly="true" />
        <?= Html::activeHiddenInput($model,'brand_id')?>
    </div>
    <div class="orders-new clearfix">
        <p>规格:</p>
        <input type="text" id="moving-spec" name="Moving[spec]" value="<?=$model->spec?>" style="border: none; background: #fff;" readOnly="true" />
    </div>
    <div class="orders-new clearfix">
        <p>单位:</p>
        <input type="text" id="moving-unit_name" name="Moving[unit_name]" value="<?=$model->unit_name?>" style="border: none; background: #fff;" readOnly="true" />
        <?= Html::activeHiddenInput($model,'unit_id')?>
    </div>
    <div class="orders-new clearfix">
        <p>条形码:</p>
        <input type="text" id="moving-barode_code" name="Moving[barode_code]" value="<?=$model->barode_code?>" style="border: none; background: #fff;" readOnly="true" />
    </div>
    <div class="orders-new clearfix">
        <p>批号:</p>
        <?=$form->field($model, 'batch_num')->textInput(['autocomplete'=>'off'])->label(false)->hint('<label></label>')?>
        <span id="b_batchnum" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>

    <h4 class="orders-newtade">调剂信息</h4>
    <div class="orders-new clearfix">
        <p>调剂数量:</p>
        <?=$form->field($model, 'number')->textInput()->label(false)->hint('<label>* </label>')?>
        <span id="tj_num" style="color: red; margin: 0; font-weight: bold;"></span>
    </div>
    <div class="orders-new clearfix">
        <p>调剂日期:</p>
        <?=$form->field($model, 'update_time')->textInput(['value'=>$model->update_time>0?date('Y-m-d', $model->update_time):date('Y-m-d', time()),'id'=>'ex-date','class'=>'laydate-icon'])->label(false)->hint('<label>*</label>')?>
    </div>
    <div class="orders-new clearfix">
        <p>备注说明:</p>
        <?= Html::activeTextarea($model,'remark',['class' => 'orders-newt2'])?>
    </div>
    <div class="orders-newbut">
        <?= Html::submitButton('保存', ['class' =>'orders-edbut']) ?>
        <a href="<?=Url::to(['moving/index'])?>">
            <span class="orders-newbut2">返回</span>
        </a>
    </div>
<?php ActiveForm::end(); ?>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $("input[name='Moving[goods_name]']").focusin(function(){

                from_warehouse_id = $('#moving-from_warehouse_id').val();
                if(from_warehouse_id!=''){
                    $("input[name='Moving[goods_name]']").bigAutocomplete({
                        width:710,
                        url:'<?=Url::to(['moving/index'])?>&action=f_goods&from_warehouse_id='+from_warehouse_id,
                        callback:function(data){
                            $("#moving-brand_id").val(data.result.brand_id);
                            $("#moving-brand_name").val(data.result.brand_name);
                            $("#moving-spec").val(data.result.spec);
                            $("#moving-unit_id").val(data.result.unit_id);
                            $("#moving-unit_name").val(data.result.unit_name);
                            $("#moving-barode_code").val(data.result.barode_code);
                            $("#moving-goods_id").val(data.result.goods_id);
                        }
                    });
                }

            });



            $("input[name='Moving[goods_name]']").blur(function(){
                var goods_name = $("input[name='Moving[goods_name]']").val();
                if(goods_name!=''){
                    $.post("<?=Url::to(['moving/index'])?>",{action:'gname',goods_name:goods_name},function(result){
                        if(result==''){
                            $("#g_name").text('商品中英文名称不存在');
                            $('.orders-edbut').attr('disabled',true);
                            return false;
                        }else{
                            $("#moving-brand_id").val(result.brand_id);
                            $("#moving-brand_name").val(result.brand_name);
                            $("#moving-spec").val(result.spec);
                            $("#moving-unit_id").val(result.unit_id);
                            $("#moving-unit_name").val(result.unit_name);
                            $("#moving-barode_code").val(result.barode_code);
                            $("#moving-goods_id").val(result.goods_id);

                            $("#moving-batch_num").val('');

                            $("#g_name").text('');
                            $('.orders-edbut').attr('disabled',false);
                        }
                    },'json');
                }else{
                    $("#moving-brand_id").val('');
                    $("#moving-brand_name").val('');
                    $("#moving-spec").val('');
                    $("#moving-unit_id").val('');
                    $("#moving-unit_name").val('');
                    $("#moving-barode_code").val('');
                    $("#moving-goods_id").val('');
                    //批次号
                    $("#moving-batch_num").val('');
                }
            });

            //根据仓库或商品中英文名过滤批号
            $("input[name='Moving[batch_num]']").focusin(function(){
                from_warehouse_id = $('#moving-from_warehouse_id').val();
                var goods_id = $('#moving-goods_id').val();
                if(goods_id!=''){
                    $(this).bigAutocomplete({
                        width:500,url:'<?=Url::to(['moving/index'])?>&action=f_batch&from_warehouse_id='+from_warehouse_id+'&goods_id='+goods_id,
                        callback:function(data){
                            $("#moving-batch_num").val(data.result.batch_num);
                            $("#moving-batch_num").attr('rel', data.result.stock_num);
                        }
                    });
                }

            });

            //验证调出仓库
            $("#moving-from_warehouse_id").blur(function(){
                if($.trim($(this).val()) == ''){
                    $("#movingwarehouse_id").html('<label class="red">请选择调出仓库</label>');
                    return false;
                }else{
                    $("#movingwarehouse_id").text('请选择仓库，前面是调出仓库，后面是调入仓库。');
                    return true;
                }
            });
            //验证调入仓库
            $("#moving-to_warehouse_id").blur(function(){
                if($.trim($(this).val()) == ''){
                    $("#movingwarehouse_id").html('<label class="red">请选择调入仓库</label>');
                    return false;
                }else{
                    $("#movingwarehouse_id").text('请选择仓库，前面是调出仓库，后面是调入仓库。');
                    return true;
                }
            });


            $("#moving-number").blur(function(){

                if($.trim($(this).val()) > parseInt($("#moving-batch_num").attr('rel'))){
                    $("#tj_num").html('<label class="red">调剂数量不能大于此批号库存数</label>');
                    $('.orders-edbut').attr('disabled',true);
                    return false;
                }else{
                    $("#tj_num").text('');
                    $('.orders-edbut').attr('disabled',false);
                }
            });

            $(".orders-edbut").click(function(){
                if($.trim($('#moving-from_warehouse_id').val()) == ''){
                    $("#movingwarehouse_id").html('<label class="red">请选择调出仓库</label>');
                    return false;
                }
                if($.trim($('#moving-to_warehouse_id').val()) == ''){
                    $("#movingwarehouse_id").html('<label class="red">请选择调入仓库</label>');
                    return false;
                }
                if($('#moving-from_warehouse_id').val()==$('#moving-to_warehouse_id').val()){
                    $("#movingwarehouse_id").html('<label class="red">调出仓库与调入仓库不能相同</label>');
                    return false;
                }
            });

        });
    </script>
<?php \frontend\components\JsBlock::end()?>