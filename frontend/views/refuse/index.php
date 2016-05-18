<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/returns.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/returns.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
		laydate({elem: '#start-date'});//绑定元素
		laydate({elem: '#end-date'});
	}();", \yii\web\View::POS_END);
$this->title = '退货入库';
$this->params['breadcrumbs'][] = $this->title;
$order = \frontend\components\Search::SearchOrder();
?>
<div class="container">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <table class="orders-info">
        <tr>
            <th>销售平台</th>
            <th>订单编号</th>
            <th>商品中英文名称（含规格）</th>
            <th>退货数量</th>
            <th>入库批次</th>
            <th>退款金额</th>
            <th>退货日期</th>
            <th>入库仓库</th>
            <th>入库状态</th>
            <th>入库日期</th>
            <th>操作</th>
        </tr>
        <tr>
            <?php foreach($dataProvider as $item){?>
            <td class="table-tdw"><?=$item['shop_name']?></td>
            <td><?=$item['order_no']?></td>
            <?php foreach($goods[$item['refuse_id']] as $v){?>
                <td class="table-tdw"><?=$v['goods_name']?>&nbsp;&nbsp;<?=$v['spec']?></td>
                <td><?=$v['number']?></td>
                <td><?=empty($v['sbatch_num'])?"无":$v['sbatch_num']?></td>
            <?php } ?>
            <td><?=$item['refuse_amount']?></td>
            <td><?=date('Y-m-d',$item['refuse_time'])?></td>
            <td><?=$item['warehouse_name']?></td>
            <td class="returns-va1"><?=($item['status']==1)? "是":"否";?></td>
            <td class="returns-va2"><?=($item['confirm_time']>0)?date('Y-m-d',$item['confirm_time']):"";?></td>
            <td>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'refuse/handle')){?>
                <div class="returns-af"><i class="iconfont returns-afs icon-queren" title="<?=($item['status']==1)?"取消确认入库":"确认入库";?>"></i>
                    <!--确认弹窗-->
                    <div class="returns-box">
                        <p class="returns-boxt1"><i class="iconfont">&#xe608;</i></p>
                        <?php if($item['status']==0) {?>
                        <p class="returns-boxt2">退货商品是否归入批号，如果归入批号请选择</p>
                        <?=Html::beginForm(Url::to(['refuse/handle','refuse_id'=>$item['refuse_id']]),'post',['id'=>'form'])?>
                        <?php foreach($goods[$item['refuse_id']] as $v){?>
                            <div class="returns-boxt3 clearfix">
                                <p ><?=$v['goods_name']?>&nbsp;&nbsp;<?=$v['spec']?></p>
                                <input type="text" class="batch_num" id="batch_num" name="batch_num[]" value=""/>
                                <input type="hidden" name="refuse_id" value="<?=$v['refuse_id']?>">
                                <input type="hidden" name="goods_id[]" value="<?=$v['goods_id']?>">
                                <input type="hidden" name="warehouse_id" value="<?=$item['warehouse_id']?>">
                                <input type="hidden" name="warehouse_name" value="<?=$item['warehouse_name']?>">
                                <input type="hidden" name="number[]" value="<?=$v['number']?>">
                            </div>
                        <?php } ?>
                        <div class="returns-but">
                            <span class="returns-but1">确定</span>
                            <span class="returns-but2">取消</span>
                        </div>
                       <?=Html::endForm()?>
                        <?php }elseif($item['status']==1){?>
                            <p class="returns-boxt2">取消退货入库操作？</p>
                            <div class="returns-boxt3 clearfix"></div>
                            <div class="returns-but">
                                <a href="<?=Url::to(['refuse/handle','refuse_id'=>$item['refuse_id'],'action'=>'cancle'])?>" data-method="post"><span class="returns-but1">确定</span></a>
                                <span class="returns-but2">取消</span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'refuse/view')){?>
                <a href="<?=Url::to(['refuse-order/view','id'=>$item['refuse_id']])?>" data-method="post"><i class="iconfont">&#xe60b;</i></a>
               <?php }?>
            </td>
        </tr>
        <?php }?>
    </table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pages,
    ]);?>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $('.returns-but1').click(function(){
                $('#form').submit();
            });
            //订单编号数据过滤
            $("input[name='order_no']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($order as $v){?>
                    {title:"<?=$v['order_no']?>"},
                    <?php }?>
                ],
            });
            $('.batch_num').each(function(){
               $(this).focus(function(){
                   var goods_id = $(this).siblings('input[name="goods_id[]"]').val();
                   var ware= $(this).siblings('input[name="warehouse_id"]').val();
                   $(this).bigAutocomplete({
                       width:149,url:'<?=Url::to(['reset/search'])?>&goods_id='+goods_id+'&warehouse_id='+ware,
                   });
               });

                $(this).keyup(function(){
                    var goods_id = $(this).siblings('input[name="goods_id[]"]').val();
                    var ware= $(this).siblings('input[name="warehouse_id"]').val();
                    $(this).bigAutocomplete({
                        width:149,url:'<?=Url::to(['reset/search'])?>&goods_id='+goods_id+'&warehouse_id='+ware,
                    });
                });
            });
        });
    </script>
    <?php \frontend\components\JsBlock::end()?>


