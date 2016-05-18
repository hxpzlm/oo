<?php

/* @var $this yii\web\View */

$this->title = '采购订单-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
?>

<!--内容-->
<div class="container">
	<div class="orders-look clearfix">
		<p class="orders-lookt1">入库仓库:</p>
		<p class="orders-lookt2"><?=$model->warehouse_name?></p>
	</div>

	<h4 class="orders-newtade">商品信息</h4>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">商品中英文名称:</p>
		<p class="orders-lookt2"><?=$model_pg['goods_name']?></p>
	</div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">规格:</p>
        <p class="orders-lookt2"><?=$model_pg['spec']?></p>
    </div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">品牌:</p>
		<p class="orders-lookt2"><?=$model_pg['brand_name']?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">单位:</p>
		<p class="orders-lookt2"><?=$model_pg['unit_name']?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">条形码:</p>
		<p class="orders-lookt2"><?=$model_pg['barode_code']?></p>
	</div>
    <h4 class="orders-newtade">采购信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">采购单价:</p>
        <p class="orders-lookt2"><?=$model_pg->buy_price?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">采购数量:</p>
        <p class="orders-lookt2"><?=$model_pg->number?></p>
    </div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">总价:</p>
		<p class="orders-lookt2"><?=$model->totle_price?> 元</p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">失效日期:</p>
		<p class="orders-lookt2"><?=date('Y-m-d',$model->invalid_time)?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">批号:</p>
		<p class="orders-lookt2"><?=$model->batch_num?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">采购日期:</p>
		<p class="orders-lookt2"><?=date('Y-m-d',$model->buy_time)?></p>
	</div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">供应商:</p>
        <p class="orders-lookt2"><?=$model_pg->supplier_name?></p>
    </div>
	<div class="orders-look clearfix">
		<p class="orders-lookt3">发票和付款情况:</p>
		<p class="orders-lookt4"><?=$model->invoice_and_pay_sate?></p>
	</div>
	<h4 class="orders-newtade">其他信息</h4>
	<div class="orders-look clearfix">
		<p class="orders-lookt1">负责人:</p>
		<p class="orders-lookt2"><?=$model->principal_name?></p>
	</div>
	<div class="orders-look clearfix">
		<p class="orders-lookt3">备注说明:</p>
		<p class="orders-lookt4"><?=$model->remark?></p>
	</div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">入库状态:</p>
        <p class="orders-lookt2"><?=$model->purchases_status==1 ? '是' : '否'?></p>
    </div>
    <h4 class="orders-newtade">系统信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建人:</p>
        <p class="orders-lookt2"><?=$model->add_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建时间:</p>
        <p class="orders-lookt2"><?=date('Y-m-d H:i:s',$model->create_time)?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">审核人:</p>
        <p class="orders-lookt2"><?=$model->confirm_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">审核时间:</p>
        <p class="orders-lookt2"><?=$model->confirm_time>0?date('Y-m-d H:i:s',$model->confirm_time):''?></p>
    </div>
	<div class="orders-lookbut">
		<a href="javascript:" onclick="window.history.go(-1);">
			<button class="orders-lookut" type="button">返回</button>
		</a>
	</div>
</div>

