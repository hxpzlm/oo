<?php

/* @var $this yii\web\View */

$this->title = '库存入库列表';
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/proware.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/proware.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
		laydate({elem: '#start-date'});//绑定元素
		laydate({elem: '#end-date'});
	}();", \yii\web\View::POS_END);
$this->title = '采购入库';
$this->params['breadcrumbs'][] = $this->title;
$goods = \frontend\components\Search::SearchGoods();
$brand = \frontend\components\Search::SearchBrand();
$supplier = \frontend\components\Search::SearchSupplier();
?>
<!--内容-->
<div class="container">
	<?php echo $this->render('_search'); ?>
	<table class="orders-info">
		<tr>
			<th>仓库</th>
			<th>商品中英文名称（含规格）</th>
			<th>品牌</th>
			<th>采购单价</th>
			<th>采购数量</th>
			<th>总价</th>
			<th>采购日期</th>
			<th>供应商</th>
			<th>入库状态</th>
			<th>入库日期</th>
			<th>操作</th>
		</tr>
		<?php foreach($dataProvider as $item){?>
		<tr>
			<td width="7%" class="table-left">&nbsp;<?=$item['warehouse_name']?></td>
			<td class="table-left"><?=$item['goods_name']?> <?=$item['spec']?></td>
			<td width="12%"><?=$item['brand_name']?></td>
			<td width="7%" class="table-right"><?=$item['buy_price']?>元</td>
			<td width="7%"><?=$item['number'].$item['unit_name']?></td>
			<td width="7%" class="table-right"><?=$item['totle_price']?>元</td>
			<td width="8%"><?=($item['buy_time']>0)?date('Y-m-d',$item['buy_time']):"";?></td>
			<td width="12%" class="table-left"><?=$item['supplier_name']?></td>
			<td width="5%"><?=($item['purchases_status']==1)?"是":"否";?></td>
			<td width="5%"><?=($item['purchases_time']>0)?date('Y-m-d',$item['purchases_time']):"";?></td>
			<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'cstocks/handle')) {?>
			<td width="5%">
				<div class="sellDe-del"><i class="iconfont sellDe-deli icon-<?if($item['purchases_status']==1){?>quxiao<?php }else{ ?>queren<?php } ?>"  title="<?=($item['purchases_status']==1)?"取消确认入库":"确认入库";?>"></i>
					<div class="sellDe-delbox">
						<p class="delboxt1"><i class="iconfont">&#xe608;</i></p>
						<?php if($item['purchases_status']==1){?>
						<p class="delboxt2">确认取消该采购入库操作？</p>
						<div class="pr-delboxt3">
							<span class="delbox-but2">取消</span>
							<a href="<?=Url::to(['cstocks/handle','id'=>$item['purchase_id'],'action'=>'cancle'])?>" data-method="post"><span class="delbox-but">确定</span></a>
						</div>
						<?php };if($item['purchases_status']==0){?>
							<p class="delboxt2">确认采购入库操作？</p>
							<div class="pr-delboxt3">
								<span class="delbox-but2">取消</span>
								<a href="<?=Url::to(['cstocks/handle','id'=>$item['purchase_id'],'action'=>'comfirm'])?>" data-method="post"><span class="delbox-but">确定</span></a>
							</div>
						<?php }?>
					</div>
				</div>
				<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'purchase/view')){?>
				<a href="<?=Url::to(['purchase/view','id'=>$item['purchase_id']])?>" data-method="post"><i class="iconfont">&#xe60b;</i></a>
				<?php }?>
			</td>
			<?php }?>
		</tr>
		<?php }?>
	</table>

	<?php echo \frontend\components\PageWidget::widget([
		'pagination' => $pages,
	]);?>
</div>
<?php \frontend\components\JsBlock::begin()?>
<script>
	$(function(){
		//商品数据过滤

		$("#goods_name").bigAutocomplete({
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
		//品牌数据过滤


		$("input[name='brand_name']").bigAutocomplete({
			width:200,data:[
				<?php foreach($brand as $v){?>
				{title:"<?=$v['name']?>"},
				<?php }?>
			],
		});

		//供应商列表

		$("input[name='supplier_name']").bigAutocomplete({
			width:200,data:[
				<?php foreach($supplier as $v){?>
				{title:"<?=$v['name']?>"},
				<?php }?>
			],
		});

	});
</script>
<?php \frontend\components\JsBlock::end()?>




