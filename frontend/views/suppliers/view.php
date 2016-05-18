<?php

/* @var $this yii\web\View */

$this->title = '供应商-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <h4 class="orders-newtade">供应商基本信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">供应商名称:</p>
        <p class="orders-lookt2"><?=$model->name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">国别:</p>
        <p class="orders-lookt2"><?=$model->country?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">城市名:</p>
        <p class="orders-lookt2"><?=$model->city?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">联系人:</p>
        <p class="orders-lookt2"><?=$model->contact_man?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">电话:</p>
        <p class="orders-lookt2"><?=$model->mobile?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">传真:</p>
        <p class="orders-lookt2"><?=$model->fax?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">邮箱:</p>
        <p class="orders-lookt2"><?=$model->email?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">地址:</p>
        <p class="orders-lookt2"><?=$model->address?></p>
    </div>
    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">负责人:</p>
        <p class="orders-lookt2"><?=$model->shop_manage_principal?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">顺序:</p>
        <p class="orders-lookt2"><?=$model->sort?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">备注说明:</p>
        <p class="orders-lookt4"><?=$model->remark?></p>
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
    <div class="orders-lookbut">
        <a href="<?=Url::to(['suppliers/index'])?>">
            <button class="orders-lookut" type="button">返回</button>
        </a>
    </div>
</div>
