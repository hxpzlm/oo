<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 17:16
 */
use yii\helpers\Html;
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->title = '修改资料';
?>
<div class="container">
    <?=Html::beginForm('','post',['name'=>'form'])?>
    <h4 class="orders-newtade">账号信息</h4>
    <div class="orders-new clearfix">
        <p>账号:</p>
        <input type="hidden" name="username" value="<?=$user['username'];?>"/><?=$user['username'];?>
    </div>
    <div class="orders-new clearfix">
        <p>旧密码:</p>
        <input type="password" name="oldpwd" />
        <span>设置帐号的密码。如果不修改密码则三个密码框留空。</span>
    </div>
    <div class="orders-new clearfix">
        <p>新密码:</p>
        <input type="password" name="pwd"/>
    </div>
    <div class="orders-new clearfix">
        <p>确认密码:</p>
        <input type="password" name="repwd"/>
    </div>
    <h4 class="orders-newtade">用户信息</h4>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <input type="text" name="real_name" value="<?=$user['real_name'];?>"/>
    </div>
    <div class="orders-new clearfix">
        <p>性别:</p>
        <div class="aaa" id="customers-sex" name="Customers[sex]">
            <input type="radio" name="sex" value="1" <?=($user['sex']==1)? 'checked="checked"':"";?> /><label>男</label>
            <input type="radio" name="sex" value="0" <?=($user['sex']==0)? 'checked="checked"':"";?>/><label>女</label>
        </div>



    </div>
    <div class="orders-new clearfix">
        <p>电话:</p>
        <input type="text" name="mobile" value="<?=$user['mobile'];?>"/>
    </div>
    <div class="orders-new clearfix">
        <p>邮箱:</p>
        <input type="text" name="email" value="<?=$user['email'];?>"/>
    </div>
    <div class="orders-newbut">
        <?=Html::submitButton("保存")?>
        <a href="<?=\yii\helpers\Url::to(['user/index'])?>">
            <button class="orders-newbut2" type="button">返回</button>
        </a>
    </div>
    <?=Html::endForm();?>
</div>


