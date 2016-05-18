<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
use frontend\models\RefuseModel;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/index.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/highcharts.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/grid.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/index.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->title = '首页';
$user = Yii::$app->user->identity;
$sale = \frontend\components\menuHelper::getSale();
$arr = \frontend\components\menuHelper::getStocksPurchase();
$arrs = \frontend\components\menuHelper::getSaleStocks();
$order = \frontend\components\Search::SearchOrder();
$query = new \yii\db\Query();
if(Yii::$app->user->identity->store_id>0){
    $store_id=" and store_id=".Yii::$app->user->identity->store_id;
}else{
    $store_id="";
}
$delivery = $query->select('delivery_id,name')->from('s2_expressway')->where('status=1 '.$store_id)->all();
?>

<!--主体内容s-->
<div class="subject_top">
	<div class="subject_top_top">
		<i></i>
		<span>待审事项</span>
	</div>
	<?php if(Yii::$app->authManager->checkAccess($user->id,'cstocks/index')) {?>
	<div class="subject_top_main">
		<div class="subject_top_left">
			<?php $val = \frontend\components\menuHelper::getPGoodsData();?>
			<label><?=$val['msg']?></label>
			<span>采购入库</span>
		</div>
		<ul class="subject_top_right">
			<?php  foreach($val['data'] as $v) {?>
			<li>
				<div>
					<h1>
						<?=$v['goods_name']?>    <?=$v['spec']?>
					</h1>
					<h2>
						品牌：<?=$v['brand_name']?>       供应商：<?=$v['supplier_name']?>
					</h2>
				</div>
				<span><?=$v['totle_price'];?></span>
				<a class="iconfont subject_top_rightbox_btn">&#xe612;</a>
				<a class="iconfont" href="<?=\yii\helpers\Url::to(['purchase/view','id'=>$v['purchase_id']]);?>">&#xe60b;</a>
				<ul class="subject_top_rightbox subject_top_rightbox01">
					<li><a><i class="iconfont">&#xe608;</i></a></li>
					<li class="subject_top_rightbox01_02">确认取消该采购入库操作？</li>
					<li>
						<input type="button" value="取消">
						<a class = "subject_top_rightbox01_02btn" href="<?=\yii\helpers\Url::to(['cstocks/handle','id'=>$v['purchase_id'],'action'=>'comfirm']);?>"><input type="button" value="确认" class="subject_flaotBtn"></a>
					</li>
				</ul>
			</li>
			<?php }?>
		</ul>
		<a href="<?=\yii\helpers\Url::to(['cstocks/index']);?>">更多>></a>
	</div>
	<?php }?>
	<?php if(Yii::$app->authManager->checkAccess($user->id,'sale/comfirm')){?>
	<div class="subject_top_main">
		<div class="subject_top_left">
			<?php $val = \frontend\components\menuHelper::getOrderData();?>
			<label><?=$val['msg']?></label>
			<span>销售出库</span>
		</div>
		<ul class="subject_top_right">
			<?php  foreach($val['data'] as $v) {?>
			<li>
				<div>
					<h1>
						<?=$v['shop_name']?>
					</h1>
					<h2>
						订单编号：<?=$v['order_no']?>
					</h2>
				</div>
				<span><?=$v['real_pay']?>元</span>
				<a class="iconfont subject_top_rightbox_btn">&#xe612;</a>
				<a class="iconfont" href="<?=\yii\helpers\Url::to(['order/view','id'=>$v['order_id']]);?>">&#xe60b;</a>
				<ul class="subject_top_rightbox subject_top_rightbox02">
					<li><a><i class="iconfont">&#xe608;</i></a></li>
					<?=Html::beginForm(Url::to(['sale/handle','order_id'=>$v['order_id'],'action'=>'confirm']),'post',['id'=>'form2'])?>
					<input type="hidden" name="order_id" value="<?=$v['order_id']?>">
					<li>
						物流公司：
						<select name="delivery_id" id="selector">
                                <option value="">请选择</option>
                                <?php foreach($delivery as $ex){?>
                                <option value="<?=$ex['delivery_id']?>"><?=$ex['name']?></option>
                                <?php }?>
                        </select>
					</li>
					<li>
						物流单号：
						<input class="danhao" type="text" name="delivery_code"/>
					</li>
					<li>
						<input type="button" value="取消">
						<input type="button" value="确认" class="subject_flaotBtn">
					</li>
					<?=Html::endForm();?>
				</ul>
				
			</li>
			<?php }?>
		</ul>
		<a href="<?=\yii\helpers\Url::to(['sale/comfirm']);?>">更多>></a>
	</div>
	<?php } ?>
	<?php if(Yii::$app->authManager->checkAccess($user->id,'refuse/index')) {?>
	<div class="subject_top_main">
		<div class="subject_top_left">
			<?php $val = \frontend\components\menuHelper::getRefuseData();?>
			<label><?=$val['msg']?></label>
			<span>退货入库</span>
		</div>
		<ul class="subject_top_right">
			<?php  foreach($val['data'] as $v) {?>
			<li>
				<div>
					<h1>
						<?=$v['shop_name']?>;
					</h1>
					<h2>
						订单编号：<?=$v['order_no']?>
					</h2>
			 	</div>
				<span><?=$v['refuse_amount']?>元</span>
				<a class="iconfont subject_top_rightbox_btn">&#xe612;</a>
				<a class="iconfont">&#xe60b;</a>
				<ul class="subject_top_rightbox subject_top_rightbox02 subject_top_rightbox03">
					<li><a><i class="iconfont">&#xe608;</i></a></li>
					<?=Html::beginForm(Url::to(['refuse/handle','refuse_id'=>$v['refuse_id']]),'post',['id'=>'form3'])?>
                        
						<?php foreach(RefuseModel::getRefuseGoods($v['refuse_id']) as $item){?>
					<li>
						<input type="hidden" name="refuse_id" value="<?=$v['refuse_id']?>">
                        <input type="hidden" name="goods_id[]" value="<?=$item['goods_id']?>">
                        <input type="hidden" name="warehouse_id" value="<?=$v['warehouse_id']?>">
                        <input type="hidden" name="warehouse_name" value="<?=$v['warehouse_name']?>">
                        <input type="hidden" name="number[]" value="<?=$item['number']?>">
						<label><?=$item['goods_name']?>&nbsp;&nbsp;<?=$item['spec']?></label>
						<input type="text" class="batch_num" id="batch_num" name="batch_num[]" value=""/>
					</li>
						<?php }?>
					<li>
						<input type="button" value="取消">
						<input type="button" value="确认" class="subject_flaotBtn">
					</li>
					<?=Html::endForm()?>
				</ul>
			</li>
          <?php } ?>
		</ul>
		<a href="<?=\yii\helpers\Url::to(['refuse/index']);?>">更多>></a>
	</div>
	<?php }?>
</div>
<?php if(Yii::$app->user->identity->store_id>0){?>
<div class="subject_bottom">
	<div class="subject_bottom_top">
		<i></i>
		<span>统计表</span>
	</div>
	<div class="subject_bottom_main" id="container1"></div>
	<div class="subject_bottom_main" id="container2"></div>
	<div class="subject_bottom_main" id="container3"></div>
</div>
<?php  \frontend\components\JsBlock::begin()?>
<script>
$(function(){
	var myDate = new Date();
	var year = myDate.getFullYear();
    $('#container1').highcharts({
        title: {
            text: year+'销售数量',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },
        xAxis: {
            categories: ['一月份', '二月份', '三月份', '四月份', '五月份', '六月份','七月份', '八月份', '九月份', '十月份', '十一一月份', '十二一月份']
        },
        yAxis: {
            title: {
                text: '商品数量(SKU)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: '件'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [
            <?php foreach($sale as $s){
			  $nums = [0,0,0,0,0,0,0,0,0,0,0,0];
			  foreach($s['data'] as $k=>$v){
				  $nums[intval($v['smonth'])-1] = intval($v['sale_nums']);
			  }
			?>
			{
            name: '<?=$s['name']?>',
            data: [<?php foreach($nums as $key=>$val){if($key<count($nums)-1 && $key>0) echo ',';echo $val;}?>]
       		 },
			<?php }?>
		]
    });
});
</script>
<script>
	$(function () {
		$('#container3').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '库存和采购数量'
			},
			xAxis: {
				categories: [<?php foreach($arr as $k=>$v){ if($k>0) echo ',';echo '"'.$v['name'].'"';}?>]
			},
			yAxis: {
				min: 0,
				title: {
					text: ''
				},
				stackLabels: {
					enabled: true,
					style: {
						fontWeight: 'bold',
						color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
					}
				}
			},
			legend: {
				align: 'right',
				x: -70,
				verticalAlign: 'top',
				y: 20,
				floating: true,
				backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
				borderColor: '#CCC',
				borderWidth: 1,
				shadow: false
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.x +'</b><br/>'+
						this.series.name +': '+ this.y +'<br/>';
				}
			},
			plotOptions: {
				column: {
					stacking: 'normal',
					dataLabels: {
						enabled: true,
						color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
					}
				}
			},
			series: [{
				name: '库存',
				data: [<?php foreach($arr as $k=>$v){ if($k>0) echo ',';echo $v['t_totle'];}?>]
			}, {
				name: '采购',
				data: [<?php foreach($arr as $k=>$v){if($k>0) echo ',';echo $v['p_totle'];}?>]
			}]
		});
	});
</script>
	<script>
		$(function () {
			$('#container2').highcharts({
				chart: {
					type: 'column'
				},
				title: {
					text: '库存和销售数量'
				},
				xAxis: {
					categories: [<?php foreach($arrs as $k=>$v){ if($k>0) echo ',';echo '"'.$v['name'].'"';}?>]
				},
				yAxis: {
					min: 0,
					title: {
						text: ''
					},
					stackLabels: {
						enabled: true,
						style: {
							fontWeight: 'bold',
							color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
						}
					}
				},
				legend: {
					align: 'right',
					x: -70,
					verticalAlign: 'top',
					y: 20,
					floating: true,
					backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
					borderColor: '#CCC',
					borderWidth: 1,
					shadow: false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.x +'</b><br/>'+
							this.series.name +': '+ this.y +'<br/>';
					}
				},
				plotOptions: {
					column: {
						stacking: 'normal',
						dataLabels: {
							enabled: true,
							color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
						}
					}
				},
				series: [{
					name: '库存',
					data: [<?php foreach($arrs as $k=>$v){if($k>0) echo ',';echo ($v['t_totle']>0)?$v['t_totle']:0;}?>]
				},{
					name: '销售',
					data: [<?php foreach($arrs as $k=>$v){ if($k>0) echo ',';echo ($v['totle']>0)?$v['totle']:0;}?>]
				}]
			});
		});
</script>
<?php  \frontend\components\JsBlock::end()?>
<?php } ?>
<?php  \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
		
		$('#selector').change(function(){
			if(!$(this).val()){
				alert("物流公司不能为空");
			}
			
		});

        $('#form2 .subject_flaotBtn').click(function(){
            var code =  $('#form2 input[name="delivery_code"]').val();
            if(!code){ alert("订单号不能为空");return false;}
            $('#form2').submit();
        });
        
    });
</script>

 <script>
        $(function(){
            //订单编号数据过滤
            $('#form3 .subject_flaotBtn').click(function(){
				$('#form3').submit();
			});
            $('.batch_num').each(function(){
               $(this).focus(function(){
				   //$('.bigautocomplete-layout').css({"display": "block","width": "176px","top": $('.batch_num').offset().top+"px","left": "1645.19px"});
                   var goods_id = $(this).siblings('input[name="goods_id[]"]').val();
                   var ware= $(this).siblings('input[name="warehouse_id"]').val();
                   $(this).bigAutocomplete({
                       width:176,url:'<?=Url::to(['reset/search'])?>&goods_id='+goods_id+'&warehouse_id='+ware,
                   });
               });

                $(this).keyup(function(){
                    var goods_id = $(this).siblings('input[name="goods_id[]"]').val();
                    var ware= $(this).siblings('input[name="warehouse_id"]').val();
                    $(this).bigAutocomplete({
                        width:176,url:'<?=Url::to(['reset/search'])?>&goods_id='+goods_id+'&warehouse_id='+ware,
                    });
                });
            });
        });
    </script>

<?php  \frontend\components\JsBlock::end()?>