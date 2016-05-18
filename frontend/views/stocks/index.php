<?php

/* @var $this yii\web\View */

$this->title = '库存入库列表';
use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/stocksData.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/stocksData.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$goodsdata = \frontend\components\Search::SearchGoods();
$brand = \frontend\components\Search::SearchBrand();
?>

<!--内容-->
<div class="container">
	<?php echo $this->render('_search'); ?>
	<table class="orders-info">
		<tr>
		    <th>仓库名</th>
		    <th>商品中英文名称（含规格）</th>
		    <th>品牌</th>
		    <th>条形码</th>
		    <th>商品所属分类</th>
		    <th>批次</th>
		    <th>库存数量</th>
		    <th>操作</th>
		</tr>
		<?php foreach($dataProvider as $item){?>
		<tr>
		    <td><?=$item['warehouse_name']?></td>
		    <td class="table-tdw"><?=$item['goods_name']?>&nbsp;&nbsp; <?=$item['spec']?></td>
		   	<td><?=$item['brand_name']?></td>
		   	<td><?=$item['barode_code']?></td>
		   	<td><?=$item['cat_name']?></td>
		   	<td><?=$item['batch_nums']?></td>
		   	<td><?=$item['totle']?><?=$item['unit_name']?></td>
			<td>
				<div class="stocksData-more"><i class="more-dian iconfont">&#xe610;</i>
					<div class="stocksD-morebox">
						<p class="moreboxt1"><i class="iconfont">&#xe608;</i></p>
						<ul class="more-lsit">
							<li class="more-lsit1 clearfix"><p>批号</p><p>采购数量</p><p>失效日期</p><p>采购日期</p><p>库存数量</p></li>
							<?php $goods= \frontend\models\StocksModel::getGoodsStocks($item['goods_id'],$item['warehouse_id']);foreach($goods as $v){ ?>
							<li class="clearfix"><p><?=empty($v['batch_num'])?"无":$v['batch_num'];?></p><p><?=$v['purchase_num']?><?=$item['unit_name']?></p><p><?=date('Y-m-d',strtotime($v['batch_num']))?></p><p><?=date('Y-m-d',$v['purchase_time'])?></p><p><?=$v['stock_num']?><?=$item['unit_name']?></p></li>
							<?php }?>
						</ul>
					</div>
				</div>
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
	   //商品数据过滤

	   $("#goods_name").bigAutocomplete({
		   width:510,data:[
			   <?php foreach($goodsdata as $val){?>
			   {title:"<?=$val['name']?>"},
			   <?php }?>
			   ],
		   });
		//品牌数据过滤


	   $("input[name='brand_name']").bigAutocomplete({
		   width:200,data:[
			   <?php foreach($brand as $v){?>
			   {title:"<?=$v['name']?>"},
			   <?php }?>
		   ],
	   });


   });
</script>
<?php \frontend\components\JsBlock::end()?>




