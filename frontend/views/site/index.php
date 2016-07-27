<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/copy_index.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/highcharts.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/grid.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/index.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->title = '首页';
$user = Yii::$app->user->identity;
$sales = \frontend\components\menuHelper::getSales();//年度销售数据
$sale = \frontend\components\menuHelper::getSale();//每月销售数据
$arr = \frontend\components\menuHelper::getStocksPurchase();//库存和采购数据
$refuse = \frontend\components\menuHelper::getRefuse();//年度退货数据
$query = new \yii\db\Query();

if(Yii::$app->user->identity->store_id>0){
    $store_id=" and store_id=".Yii::$app->user->identity->store_id;
}else{
    $store_id="";
}
$ware = \frontend\models\WarehouseModel::find()->where('status=1 '.$store_id)->all();
$delivery = $query->select('delivery_id,name')->from('s2_expressway')->where('status=1 '.$store_id)->all();
?>

<!--主体内容s-->
<div class="subject">
	<div class="subject_main">
		<div class="top">
			<!--待审事项-->
			<div class="top_l box_parent">
				<div>
					<i class="i"></i>
					<span>待审事项</span><span class="top_l_yuan"><?=$p+$s?></span>
				</div>
				<div class="top_l_t list_js">
					<span class="border_link">采购销售待审(<?=$p?>)</span>
					<span>其他待审(<?=$s?>)</span>
				</div>
				<ul class="top_l_box1 top_l_js show">
					<?php $i=1;foreach ($data as $v){
						if($i<=10){?>

					<?php if(isset($v['purchase_id'])){
					?>
					<?php if(Yii::$app->authManager->checkAccess($user->id,'cstocks/index')) {?>
                      <li>
						<a class="iconfont icon_top_l" href="<?=Url::to(['purchase/index'])?>">&#xe61a;</a>
						<div class="top_l_p">
							<?php $pgoods = \frontend\models\PurchaseGoods::find()->select('goods_id,goods_name,spec,number,unit_name')->where(['purchase_id'=>$v['purchase_id']])->one()?>
							<p><?=$pgoods->goods_name?>&nbsp;&nbsp;<?=$pgoods->spec?>&nbsp;&nbsp;<?=$pgoods->number?><?=$pgoods->unit_name?></p>
							<p class="top_l_p_bottom">品牌：<?=$pgoods->brand_name?>   单价：<?=$pgoods->buy_price?>元   采购日期：<?=date("Y-m-d",$v['time'])?>   仓库：<?=$v['warehouse_name']?></p>
						</div>
						<div class="top_l_span">
							<span class="iconfont icon_top_l_r">&#xe60b;</span><span class="iconfont icon_cai icon_top_l_r">&#xe612;</span>
						</div>
						<div class="clear"></div>

						<!--入库弹窗-->
						<div class="panel_1">
							<p>确认将该商品采购入库？</p>
							<a href="<?=Url::to(['cstocks/handle','id'=>$v['purchase_id'],'action'=>'comfirm'])?>" data-method="post"><button class="button sure">确认</button></a>
							<span class="button cancel">取消</span>
						</div>

						<!--入库弹窗end-->
						</li><?php } $i++; ?>
					<?php }elseif(isset($v['order_id'])){?>
						<?php if(Yii::$app->authManager->checkAccess($user->id,'sale/comfirm')){?>
							<li>
								<a class="iconfont icon_top_l" href="<?=Url::to(['order/index'])?>">&#xe61e;</a>
								<div class="top_l_p">
									<?php foreach(\frontend\models\OrderGoods::find()->select('goods_name,spec,number,unit_name')->where(['order_id'=>$v['order_id']])->all() as $val){
									?>
									<p><?=$val['goods_name']?>&nbsp;&nbsp;<?=$val['spec']?>&nbsp;&nbsp;<?=$val['number']?><?=$val['unit_name']?></p>
									<?php } ?>
									<p class="top_l_p_bottom">订单编号：<?=$v['order_no']?>   销售金额：<?=$v['real_pay']?>元   销售日期：<?=date("Y-m-d",$v['time'])?>   仓库：<?=$v['warehouse_name']?></p>
								</div>
								<div class="top_l_span">
									<span class="iconfont icon_top_l_r">&#xe60b;</span><span class="iconfont icon_xiao icon_top_l_r">&#xe612;</span>
								</div>
								<div class="clear"></div>

								<!--物流销售弹窗-->
							<div class="panel_3">
							<?=Html::beginForm(Url::to(['sale/handle','order_id'=>$v['order_id'],'action'=>'confirm']),'post',['id'=>'form'.$v['order_id']])?>
							<input type="hidden" name="order_id" value="<?=$v['order_id']?>">
							<p class="panel_3_p1">请输入物流信息</p>
									<p>
										<select name="delivery_id">
											<option value="请选择">请选择</option>
											<?php foreach ($delivery as $val){?>
											<option value="<?=$val['delivery_id']?>"><?=$val['name']?></option>
									        <?php } ?>
										</select>物流公司：
									</p>
									<p><input class="panel_3_input" type="text" name="delivery_code"/>物流单号：</p>
									<button class="button sure">确认</button>
									<span class="button cancel">取消</>
							<?=Html::endForm();?>
								</div>
								<!--物流销售弹窗end-->
                          </li><?php } $i++;?>

					<?php }else{ ?>
							<?php if(Yii::$app->authManager->checkAccess($user->id,'refuse/handle')) {?>
                               <li>
								<a class="iconfont icon_top_l" href="<?=Url::to(['refuse-order/index'])?>">&#xe61d;</a>
								<div class="top_l_p">
									<?php
									$rgoods=\frontend\models\RefuseOrderGoods::find()->select('goods_id,goods_name,spec,number,unit_name,batch_num')->where(['refuse_id'=>$v['refuse_id']])->all();
									foreach ($rgoods as $val){?>
									<p><?=$val['goods_name']?>&nbsp;&nbsp;<?=$val['spec']?>&nbsp;&nbsp;<?=$val['number']?><?=$val['unit_name']?></p>
									<?php } ?>
									<p class="top_l_p_bottom">订单编号：<?=$v['order_no']?>   退货金额：<?=$v['refuse_amount']?>元   销售日期：<?=date("Y-m-d",$v['sale_time'])?>   仓库：<?=$v['warehouse_name']?></p>
								</div>
								<div class="top_l_span">
									<span class="iconfont icon_top_l_r">&#xe60b;</span><span class="iconfont icon_tui icon_top_l_r">&#xe612;</span>
								</div>
								<div class="clear"></div>

								<!--退货弹窗-->
								<div class="panel_2">
							<?=Html::beginForm(Url::to(['refuse/handle','refuse_id'=>$v['refuse_id']]),'post',['id'=>'form3'])?>
							<input type="hidden" name="refuse_id" value="<?=$v['refuse_id']?>">
							<input type="hidden" name="warehouse_name" value="<?=$v['warehouse_name']?>">
							<p class="panel_2_p1">退货商品是否归入批号？如果归入某批号请选择！</p>
							        <?php foreach ($rgoods as $val){?>
									<p><?=$val['goods_name']?>  &nbsp;&nbsp;<?=$val['spec']?>
										<input type="text" name="batch_num[]" autocomplete="off" class="autocomplete_bind" value="<?=$val['batch_num']?>"/>
										<input type="hidden" name="goods_id[]" value="<?=$val['goods_id']?>">
										<input type="hidden" name="warehouse_id" value="<?=$v['warehouse_id']?>">
										<input type="hidden" name="number[]" value="<?=$val['number']?>">
									</p>
							        <?php } ?>
									<button class="button sure">确认</button>
									<span class="button cancel">取消</span>
								</div>
								<!--退货弹窗end-->
							</li>

							<?php } $i++;?>
					<?php } ?>

                   <?php }

					}?>
					<div class="clear"></div>
				</ul>
				<ul class="top_l_box2 top_l_js">
					<?php $i=1;foreach ($other as $v){
						  if($i<=10){
						?>
					<li>
						<?php if(isset($v['moving_id'])){?>
						<a class="iconfont icon_top_l" href="<?=Url::to(['moving/index'])?>">&#xe61c;</a>
						<div class="top_l_p">
							<p><?=$v['from_warehouse_name']?> -> <?=$v['to_warehouse_name']?></p>
							<p>商品：<?=$v['goods_name']?>  <?=$v['spec']?>     数量：<?=$v['number']?><?=$v['unit_name']?>   调剂日期：<?=date('Y-m-d',$v['time'])?></p>
						</div>
						<div class="top_l_span">
							<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/view')){?>
								<a href="<?=Url::to(['moving/view','id' => $v['moving_id']])?>" style='color:#333',><span class="iconfont icon_top_l_r">&#xe60b;</span></a>
							<?php }?>
							<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/handle')){?>
							<?= Html::a('<span class="iconfont icon_diao icon_top_l_r">&#xe612;</span>', ['moving/handle', 'id' =>$v['moving_id'],'action'=>'comfirm'], [
								'style' => 'color:#333',
								'data' => [
									'method' => 'post',
								],
							]) ?>
							<?php }?>

						</div>
						<div class="clear"></div>

                        <?php }elseif(isset($v['check_id'])){?>
							<a class="iconfont icon_top_l">&#xe619;</a>
							<div class="top_l_p">
								<p><?=$v['warehouse_name']?> 盘点（<?=$v['check_no']?>）</p>
								<p>开单人：<?=$v['add_user_name']?>   盘点日期：<?=date('Y-m-d',$v['time'])?></p>
							</div>
							<div class="top_l_span">
								<span class="iconfont icon_top_l_r">&#xe60b;</span><span class="iconfont icon_pan icon_top_l_r">&#xe612;</span>
							</div>
							<div class="clear"></div>
							<!--盘点等弹窗-->
							<div class="panel_5">
								<p>确认进行商品盘点？</p>
								<button class="button sure">确认</button>
								<span class="button cancel">取消</span>
							</div>
						<?php }else{?>
							<a class="iconfont icon_top_l">&#xe61b;</a>
							<div class="top_l_p">
								<?php foreach (\frontend\models\OtherGoods::find()->select('goods_name,spec,number,unit_name')->where(['other_id'=>$v['other_id']])->all() as $val){?>
								<p><?=$val['goods_name']?>     <?=$val['spec']?>     <?=$val['number']?><?=$val['unit_name']?></p>
								<?php } ?>
								<p>申请人：<?=$v['add_user_name']?>   申请日期：<?=date('Y-m-d',$v['time'])?></p>

							</div>
							<div class="top_l_span">
								<span class="iconfont icon_top_l_r">&#xe60b;</span><span class="iconfont icon_chu icon_top_l_r">&#xe612;</span>
							</div>
							<div class="clear"></div>
							<div class="panel_4">
								<p>确认将该商品出库吗？</p>
								<button class="button sure">确认</button>
								<span class="button cancel">取消</span>
							</div>
						<?php } ?>
					</li>
					<?php }
						$i++;
					} ?>
					<div class="clear"></div>
				</ul>
			</div>
			<!--库存预警-->
			<div class="top_r">
				<div class="top_r_box">
					<div>
						<i class="i"></i>
						<span>库存预警</span><span class="top_l_yuan"><?=\frontend\models\WarningInfo::find()->where('close_type=0'.$store_id)->count()?></span>
					</div>

					<ul>
						<?php if(Yii::$app->authManager->checkAccess($user->id,'warning/index')) {?>
						<?php foreach (\frontend\models\WarningInfo::find()->asArray()->select('info,warning_time')->where('close_type=0'.$store_id)->limit(12)->all() as $v){?>
						<li>
							<span class="iconfont top_r_icon">&#xe614;</span>
							<div class="top_r_main">
								<p>
									<?=date('Y-m-d',$v['warning_time'])?> 预警：
								</p>
								<?=$v['info']?>
							</div>
							<div class="clear"></div>
						</li>
						<?php } ?>
						<?php } ?>
					</ul>
					<p class="iconfont top_r_d">&#xe60c;</p>

				</div>
			</div>
			<div class="clear"></div>
		</div>







		<?php if(Yii::$app->user->identity->store_id>0){?>
		<div class="down" style="margin-bottom: 10px">
		<!--统计图表-->
		<div class="down_l">
			<p>
				<i class="i"></i><span>统计图表</span>
			</p>

			<div class="down_l_img box_parent">
				<div class="down_l_l list_js">
					<span class="border_link">各仓年度销售总量</span>
					<span>各仓年度销售总量</span>
				</div>
				<div class="down_l_img1">
					<div class="show top_l_js down_l_img1_box" id="con1"></div>
					<div class="top_l_js down_l_img1_box"  id="con2"></div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="down_l_img2" id="con3">
			</div>
			<div class="clear"></div>
		</div>
		<!--同步信息-->
		<div class="down_r">
			<div>
				<i class="i"></i>
				<span>同步信息</span>
			</div>
			<div  class="down_r_main">
				<p><span class="iconfont">&#xe615;</span>同步信息功能敬请期待……</p>
			</div>
		</div>
		<div class="clear"></div>
	</div>
		<div class="clear"></div>
	</div>
</div>
<!--主体内容e-->

<?php  \frontend\components\JsBlock::begin()?>
<script>
	$(function () {
		$('#con1').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			title: {
				text: '各仓年度销售总量, <?=date('Y',time())?>'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					depth: 35,
					dataLabels: {
						enabled: true,
						color: '#000000',
						connectorColor: '#000000',
						format: '<b>{point.name}</b>: {point.percentage:.1f} %'
					}
				}
			},
			series: [{
				type: 'pie',
				name: '年度销售总量',
				data: [
					<?php foreach ($sales['data'] as $v){?>
					['<?=$v['name']?>', <?=round(($v['sale_nums']/$sales['count'])*100,2)?>],
					<?php } ?>
				]
			}]
		});

		$('#con2').highcharts({
			title: {
				text: '<?=date("Y",time())?>销售数量',
				x: -20 //center
			},
			subtitle: {
				text: '',
				x: -20
			},
			xAxis: {
				categories: ['一月份', '二月份', '三月份', '四月份', '五月份', '六月份','七月份', '八月份', '九月份', '十月份', '十一月份', '十二']
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
					data: [<?php foreach($nums as $key=>$val){if($key<count($nums) && $key>0) echo ',';echo $val;}?>]
				},
				<?php }?>
			]
		});
		$('#con3').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '<?=date('Y',time())?>年各仓库存|采购|销售|退货数量'
			},
			xAxis: {
				categories: [
					<?php foreach ($ware as $k=>$v){
					    if($k<count($ware) && $k>0) echo ",";
					    echo "'".$v['name']."'";
				    }?>

				]
			},
			yAxis: {
				min: 0,
				title: {
					text: '数量 (SKU)'
				}
			},
			credits: {
				enabled: false
			},
			series: [
			{
				name: '库存',
				data: [
				<?php foreach ($ware as $v){
					foreach ($arr as $val){
						if($v['warehouse_id']==$val['warehouse_id']){
							echo ($val['t_totle']>0)?$val['t_totle'].",":'0'.",";
						}
					}
				}?>
				]
			},
			{
					name: '采购',
					data: [
						<?php foreach ($ware as $v){
						foreach ($arr as $val){
							if($v['warehouse_id']==$val['warehouse_id']){
								echo ($val['p_totle']>0)?$val['p_totle'].",":'0'.",";
							}
						}
					}?>
					]
			},
				{
					name: '销售',
					data: [
						<?php foreach ($ware as $v){
						foreach ($sales['data'] as $val){
							if($v['warehouse_id']==$val['warehouse_id']){
								echo ($val['sale_nums']>0)?$val['sale_nums'].",":'0'.",";
							}
						}
					}?>
					]
				},
				{
					name: '退货',
					data: [
						<?php foreach ($ware as $v){
						foreach ($refuse as $val){
							if($v['warehouse_id']==$val['warehouse_id']){
								echo ($val['nums']>0)?$val['nums'].",":'0'.",";
							}
						}
					}?>
					]
				},

			]
		});
        $('.autocomplete_bind').each(function(){
			var goods_id = $(this).siblings('input[name="goods_id[]"]').val();
			var ware= $(this).siblings('input[name="warehouse_id"]').val();
			$(this).bigAutocomplete({
				width:176,url:'<?=Url::to(['reset/search'])?>&goods_id='+goods_id+'&warehouse_id='+ware,
			});
		});
	});
</script>
<?php  \frontend\components\JsBlock::end() ?>
<?php }?>

