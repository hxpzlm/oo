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
            <th>批号</th>
            <th>采购日期</th>
            <th>供应商</th>
		    <th>入库状态</th>
		    <th>操作</th>
		</tr>
        <?php foreach($dataProvider as $item){ ?>
		<tr>
            <td><?= $item['warehouse_name']; ?></td>
            <td class="table-tdw"><?=BaseStringHelper::truncate($item['name'],32).'&nbsp;'.$item['spec'];?> </td>
            <td><?=$item['brand_name'];?></td>
            <td><?=$item['barode_code'];?></td>
            <td><?=$item['buy_price'];?>元</td>
            <td><?=$item['number'];?></td>
            <td><?= $item['batch_num']; ?></td>
            <td><?= date('Y-m-d',$item['buy_time']); ?></td>
            <td><?=$item['supplier_name'];?></td>
		   	<td><?=$item['purchases_status']==1 ? '是':'否'; ?></td>

		   	<td>
                <?php if($item['purchases_status']!=1){?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'purchase/delete')){?>
                <?= Html::a('<i class="iconfont">&#xe605;</i>', ['delete', 'id' => $item['purchase_id']], [
                    'class' => 'orders-infosc',
                    'data' => [
                        'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                        'method' => 'post',
                    ],
                ]) ?>
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
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            //订单编号搜索
            $("input[name='name']").focus(function(){
                var html = '<table><tbody>';
                <?php foreach($goods as $v){?>
                html+='<tr class=""><td><div><?=$v['name']?></div></td></tr>';
                <?php }?>
                html+="</tbody></table>";
                $('.bigautocomplete-layout').html(html);
                $('.bigautocomplete-layout').css({'display':'block','width': '510px','top': '227px','left': '9.5px'});
            });
            $("input[name='name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($goods as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
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
    </script>
<?php \frontend\components\JsBlock::end()?>