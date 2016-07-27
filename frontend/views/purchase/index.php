<?php

/* @var $this yii\web\View */

$this->title = '采购订单列表';
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\BaseStringHelper;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
    laydate({elem: '#start-date'});//绑定元素
    laydate({elem: '#end-date'});
}();", \yii\web\View::POS_END);

$goods = \frontend\components\Search::SearchGoods();
$brand_list = \frontend\components\Search::SearchBrand();
$supplier_list = \frontend\components\Search::SearchSupplier();
?>
<!--内容-->
<div class="container">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
	<table class="orders-info">
		<tr>
		    <th>仓库</th>
		    <th>商品中英文名称（含规格）</th>
		    <th>品牌</th>
		    <th>条形码</th>
		    <th>采购单价</th>
		    <th>采收数量</th>
            <th>批号&nbsp;&nbsp;</th>
            <th>采购日期</th>
            <th>供应商</th>
		    <th>入库状态</th>
		    <th>操作</th>
		</tr>
        <?php foreach($dataProvider as $item){ ?>
		<tr>
            <td width="8%" class="table-left">&nbsp;&nbsp;<?= $item['warehouse_name']; ?></td>
            <td class="table-left"><?=BaseStringHelper::truncate($item['name'],32).'&nbsp;'.$item['spec'];?></td>
            <td width="10%"><?=$item['brand_name'];?></td>
            <td width="5%"><?=$item['barode_code'];?></td>
            <td width="7%" class="table-right"><?=$item['buy_price'];?>元</td>
            <td width="7%"><?=$item['number'];?></td>
            <td width="5%"><?= $item['batch_num']; ?></td>
            <td width="10%"><?= date('Y-m-d',$item['buy_time']); ?></td>
            <td width="12%" class="table-left"><?=$item['supplier_name'];?></td>
		   	<td width="5%"><?=$item['purchases_status']==1 ? '是':'否'; ?></td>
		   	<td width="5%">
                <?php if($item['purchases_status']!=1){?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'purchase/delete')){?>
                <a class="orders-infosc" href="javascript:;" nctype="<?=$item['purchase_id']?>"><i class="iconfont">&#xe605;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'purchase/update')){?>
                <a href="<?=Url::to(['purchase/update','id' => $item['purchase_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php }?>
                <?php }?>

                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'purchase/view')){?>
                <a href="<?=Url::to(['purchase/view','id' => $item['purchase_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                <?php }?>
		   	</td>
		</tr>
        <?php } ?>
	</table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pages,
    ]);?>
</div>

    <div class="orders-sc">
        <p class="orders-sct1 clearfix">删除<i class="iconfont">&#xe608;</i></p>
        <p class="orders-sct2">您确定要删除这条记录吗？删除后不可恢复！</p>
        <div class="orders-sct3">
            <a href="" data-method="post"><span class="orders-sct3qx" style="cursor: pointer">确定</span></a>
            <span style="cursor: pointer">取消</span>
        </div>
    </div>

<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            //商品中英文名称搜索
            $("input[name='goods_name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($goods as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ],
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='goods_name']").val('');
                        $(this).hide();
                    })
                }
            });

            $("input[name='brand_name']").bigAutocomplete({
                width:200,
                data:[
                    <?php foreach($brand_list as $v){?>
                    {title:"<?=$v['name']?>",result:{brand_id:"<?=$v['brand_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='brand_id']").val(data.result.brand_id);
                }
            });
            $("input[name='supplier_name']").bigAutocomplete({
                width:200,
                data:[
                    <?php foreach($supplier_list as $v){?>
                    {title:"<?=$v['name']?>",result:{supplier_id:"<?=$v['suppliers_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='supplier_id']").val(data.result.supplier_id);
                }
            });

        });
        //删除
        var sc;
        $('.orders-infosc').click(function(){
            var id = $(this).attr('nctype');
            $('.orders-sct3>a').attr('href','<?=Url::to(['purchase/delete'])?>&id='+id);
            sc = $(".orders-sc").bPopup();
        })
        $(".orders-sct1 i,.orders-sct3 span").click(function(){
            sc.close();
        });
    </script>
<?php \frontend\components\JsBlock::end()?>