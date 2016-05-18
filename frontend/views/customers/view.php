<?php

/* @var $this yii\web\View */

$this->title = '客户-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <h4 class="orders-newtade">客户基本信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">客户来源:</p>
        <p class="orders-lookt2">
            <?php
            $v = (new \yii\db\Query())->select('name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('shop_id='.$model->shop_id)->one();
            echo $v['name'];
            ?>
        </p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">客户帐号:</p>
        <p class="orders-lookt2"><?=$model->username?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">姓名:</p>
        <p class="orders-lookt2"><?=$model->real_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">性别:</p>
        <p class="orders-lookt2"><?=$model->sex==0 ? '女' : ($model->sex==2 ? '保密' : '男')?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">联系电话:</p>
        <p class="orders-lookt2"><?=$model->mobile?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">Email/QQ/其他:</p>
        <p class="orders-lookt2"><?=$model->other?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">地址:</p>
        <p class="orders-lookt2"><?=$model->address?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">客户类型:</p>
        <p class="orders-lookt2"><?=$model->type==1 ? '企业客户' : '个人客户'?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">顺序:</p>
        <p class="orders-lookt2"><?=$model->sort?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">备注说明:</p>
        <p class="orders-lookt2"><?=$model->remark?></p>
    </div>
    <h4 class="orders-newtade">收货信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">收货人信息:</p>
        <p style="width: 70%; line-height: 32px; float: left;">
            <table cellspacing="0" cellpadding="0" border="0" width="70%">
            <?php
            $res = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'address')->where('customers_id='.$model->customers_id)->all();
            foreach($res as $row){
            if($row['is_idcard']==1) {
               $checked = 'checked';
            }else{
               $checked = '';
            }
            echo '<tr><td width="15%" height="32px">'.$row['accept_name'].'</td><td width="15%">'.$row['accept_mobile'].'</td><td width="30%">'.$row['accept_address'].'</td><td>'.$row['zcode'].'</td><td width="20%">'.$row['accept_idcard'].'</td><td width="20%"><input type="checkbox" name="Address[is_idcard][]" '.$checked.' value="'.$row['is_idcard'].'" />已上传身份证</td></tr>';
            }
            ?>
            </table>
        </p>
        <div class="clear"></div>
    </div>
    <h4 class="orders-newtade">系统信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建人:</p>
        <p class="orders-lookt2">
            <?php
            $item = (new \yii\db\Query())->select('username')->from(Yii::$app->getDb()->tablePrefix.'user')->where('user_id='.$model->add_user_id)->one();
            echo $item['username'];
            ?>
        </p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建时间:</p>
        <p class="orders-lookt2"><?=date('Y-m-d H:i:s',$model->create_time)?></p>
    </div>
    <div class="orders-lookbut">
        <a href="<?=Url::to(['customers/index'])?>">
            <button class="orders-lookut" type="button">返回</button>
        </a>
    </div>
</div>