<?php

/* @var $this yii\web\View */

$this->title = '销售订单-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);

$query = new \yii\db\Query();
$tablePrefix = Yii::$app->getDb()->tablePrefix;
?>
<!--内容-->
<div class="container">
    <h4 class="orders-newtade">订单基本信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">仓库:</p>
        <p class="orders-lookt2">
            <?php
            if($model->warehouse_id>0){
                $v = $query->select('name')->from($tablePrefix.'warehouse')->where('warehouse_id='.$model->warehouse_id)->one();
                echo $v['name'];
            }
            ?>
        </p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">销售平台:</p>
        <p class="orders-lookt2">
            <?php
            if($model->shop_id>0){
                $v = $query->select('name')->from($tablePrefix.'shop')->where('shop_id='.$model->shop_id)->one();
                echo $v['name'];
            }
            ?>
        </p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">订单编号:</p>
        <p class="orders-lookt2"><?=$model->order_no?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">销售日期:</p>
        <p class="orders-lookt2"><?=date('Y-m-d',$model->sale_time)?></p>
    </div>
    <h4 class="orders-newtade">商品相关信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">商品及赠品:</p>
        <div class="goodsReceipt">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <?php
            if(!empty($goodlist)){
                foreach($goodlist as $row){
                    echo '<tr><td height="30">'.$row['name'].'</td><td>'.$row['spec'].'</td><td>'.$row['bname'].'</td><td>'.$row['batch_num'].'</td><td>'.$row['sell_price'].'&nbsp;元'.'</td><td>'.$row['number'].'&nbsp;'.$row['unit_name'].'</td></tr>';
                }
            }
            ?>
        </table>
        </div>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">实收款:</p>
        <p class="orders-lookt2"><?=$model->real_pay?> 元</p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">优惠:</p>
        <p class="orders-lookt2"><?=$model->discount?> 元</p>
    </div>
    <h4 class="orders-newtade">客户及收货信息</h4>
    <?php
    if($model->customer_id>0){
        $customer = $query->select('username,real_name')->from($tablePrefix.'customers')->where('customers_id='.$model->customer_id)->one();
    }
    ?>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">客户帐号:</p>
        <p class="orders-lookt2"><?=!empty($customer['username'])?$customer['username']:''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">姓名:</p>
        <p class="orders-lookt2"><?=!empty($customer['real_name'])?$customer['real_name']:''?></p>
    </div>
    <?php
    if($model->address_id>0){
        $address = $query->select('accept_name,accept_mobile,accept_address,accept_idcard,is_idcard,idcard_url')->from($tablePrefix.'address')->where('address_id='.$model->address_id)->one();
    }
    ?>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">收货人姓名:</p>
        <p class="orders-lookt2"><?=!empty($address['accept_name'])?$address['accept_name']:''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">电话:</p>
        <p class="orders-lookt2"><?=!empty($address['accept_mobile'])?$address['accept_mobile']:''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">地址:</p>
        <p class="orders-lookt2"><?=!empty($address['accept_address'])?$address['accept_address']:''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">身份证号码:</p>
        <p class="orders-lookt2"><?=!empty($address['accept_idcard'])?$address['accept_idcard']:''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">是否已上传身份证:</p>
        <p class="orders-lookt2"><?=!empty($address['is_idcard']) ? '是' : '否'?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">身份证上传网址:</p>
        <p class="orders-lookt2"><?=!empty($address['idcard_url'])?$address['idcard_url']:''?></p>
    </div>
    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">备注说明:</p>
        <p class="orders-lookt2"><?=$model->remark?></p>
    </div>
    <h4 class="orders-newtade">销售出库信息</h4>
    <?php
    if($model->delivery_id>0){
        $delivery = $query->select('name')->from($tablePrefix.'expressway')->where('delivery_id='.$model->delivery_id)->one();
    }
    ?>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">物流公司:</p>
        <p class="orders-lookt2"><?=!empty($delivery['name'])?$delivery['name']:''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">物流单号:</p>
        <p class="orders-lookt2"><?=!empty($model->delivery_code)?$model->delivery_code:''?></p>
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
    <div class="orders-look clearfix">
        <p class="orders-lookt1">审核人:</p>
        <p class="orders-lookt2"><?=$model->confirm_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">审核时间:</p>
        <p class="orders-lookt2"><?=$model->confirm_time>0?date('Y-m-d',$model->confirm_time):''?></p>
    </div>
    <div class="orders-lookbut">
        <a href="javascript:" onclick="window.history.go(-1);">
            <button class="orders-lookut" type="button">返回</button>
        </a>
    </div>
</div>