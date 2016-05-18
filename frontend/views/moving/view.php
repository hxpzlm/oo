<?php

/* @var $this yii\web\View */

$this->title = '库存调剂-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
?>

<!--内容-->
<div class="container">
    <h4 class="orders-newtade">仓库信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">仓库:</p>
        <p class="orders-lookt2"><?=$model->from_warehouse_name.' -> '.$model->to_warehouse_name?></p>
    </div>

    <h4 class="orders-newtade">商品信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">商品中英文名称:</p>
        <p class="orders-lookt2"><?=$model->goods_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">品牌:</p>
        <p class="orders-lookt2"><?=$model->brand_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">规格:</p>
        <p class="orders-lookt2"><?=$model->spec?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">单位:</p>
        <p class="orders-lookt2"><?=$model->unit_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">条形码:</p>
        <p class="orders-lookt2"><?=$model->barode_code?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">批号:</p>
        <p class="orders-lookt2"><?=$model->batch_num?></p>
    </div>
    <h4 class="orders-newtade">调剂信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">调剂数量:</p>
        <p class="orders-lookt2"><?=$model->number?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">调剂日期:</p>
        <p class="orders-lookt2"><?=date('Y-m-d',$model->update_time)?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">备注说明:</p>
        <p class="orders-lookt4" style="border: none"><?=$model->remark?></p>
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
        <p class="orders-lookt1">确认入库人:</p>
        <p class="orders-lookt2"><?=$model->confirm_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">入库时间:</p>
        <p class="orders-lookt2"><?=date('Y-m-d',$model->confirm_time)?></p>
    </div>
    <div class="orders-lookbut">
        <a href="<?=Url::to(['moving/index'])?>">
            <button class="orders-lookut" type="button">返回</button>
        </a>
    </div>
</div>