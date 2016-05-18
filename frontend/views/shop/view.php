<?php

/* @var $this yii\web\View */

$this->title = '销售平台-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <h4 class="orders-newtade">销售平台信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">平台名称:</p>
        <p class="orders-lookt2"><?=$model->name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">状态:</p>
        <p class="orders-lookt2"><?=$model->status==1 ? '启用' : '停用'?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">顺序:</p>
        <p class="orders-lookt2"><?=$model->sort?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">备注说明:</p>
        <p class="orders-lookt2"><?=$model->remark?></p>
    </div>
    <h4 class="orders-newtade">系统信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建人:</p>
        <p class="orders-lookt2"><?=$model->add_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建时间:</p>
        <p class="orders-lookt2"><?=date('Y-m-d',$model->create_time)?></p>
    </div>
    <div class="orders-lookbut">
        <a href="<?=Url::to(['shop/index'])?>">
            <button class="orders-lookut" type="button">返回</button>
        </a>
    </div>
</div>