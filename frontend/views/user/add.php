<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 17:16
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->title = '添加用户-用户权限';
$data = frontend\components\menuHelper::getAuthData();
$wh['status'] = 1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id'] = Yii::$app->user->identity->store_id;
}
$store = \frontend\models\Store::findAll($wh);
?>
<div class="container">
    <?php $form = ActiveForm::begin([
		'id' => 'form-signup',
		'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ]); ?>
    <h4 class="orders-newtade">账号信息</h4>
    <div class="orders-new clearfix">
        <p>账号:</p>
        <?=$form->field($model, 'username')->textInput()->label(false)->hint('<label>* 帐号不可重复！新建帐号的初始密码为vtg123</label>')?>
    </div>
    <div class="orders-new clearfix">
        <p>入驻商家:</p>
        <select name="SignupForm[store_id]">
            <?php foreach ($store as $item) {?>
            <option value="<?=$item['store_id']?>"><?=$item['name']?></option>
            <?php }?>
        </select>
        <label> &nbsp;* </label>
    </div>
    <div class="orders-new clearfix">
        <p>状态:</p>
        <div class="jurisdiction_top">
            <input type="radio" name="SignupForm[status]" value="1" checked="checked"/><label>正常</label>
            <input type="radio" name="SignupForm[status]" value="0" /><label>停用</label>
			<label> * 停用后该帐号将不允许登录系统。</label>
        </div>
		
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <input type="text" name="SignupForm[sort]" value="999"/>
    </div>
    <h4 class="orders-newtade">用户信息</h4>
    <div class="orders-new clearfix">
        <p>姓名:</p>
		<?= $form->field($model, 'real_name')->textInput()->label(false)->hint('<label>* </label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>性别:</p>
        <div class="jurisdiction_top">
            <input type="radio" name="SignupForm[sex]" value="1" checked="checked" /><label>男</label>
            <input type="radio" name="SignupForm[sex]" value="0" /><label>女</label>
			<label>*</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>电话:</p>
        <?= $form->field($model, 'mobile')->textInput()->label(false)->hint('<label>* 获取系统验证码，请务必输入正确</label>') ?>
    </div>
    <div class="orders-new clearfix">
        <p>邮箱:</p>
        <input type="text" name="SignupForm[email]" value=""/>
    </div>
    <h4 class="orders-newtade">权限信息</h4>
    <div class="orders-new clearfix">
        <p class="orders-newt1 jurisdiction_left">账号权限:</p>
        <div class="jurisdiction">
            <div class="jurisdiction_top">
                <input type="radio" name="SignupForm[type]" value="2" checked="checked" id ="jurisdiction01"/><label>普通管理员</label>
				<?php if(Yii::$app->user->identity->store_id==0){?>
                <input type="radio" name="SignupForm[type]" value="1" id ="jurisdiction02"/><label>系统管理员</label>
				<?php }?>
            </div>
            <div class="clearfix"></div>
             <div id="jurisdiction_box">
            <table class="table01">
                <thead><td></td></thead>
                <tbody>
                <tr><td>首页</td></tr>
                <tr style="height: 54px; line-height: 54px"><td>采购</td></tr>
                <tr style="height: 108px; line-height: 108px"><td>销售</td></tr>
                <tr style="height: 216px; line-height: 216px"><td>库存</td></tr>
				 <tr style="height: 135px; line-height: 135px"><td>统计</td></tr>
                <tr style="height: 108px; line-height: 108px"><td>商品</td></tr>
                <tr style="height: 54px; line-height: 54px"><td>系统设置</td></tr>
                </tbody>
            </table>
            <table class="table02">
                <thead>
                <td> </td>
                <td>首页权限</td>
                <td>查看权限</td>
                <td>编辑权限</td>
                <td>新建权限</td>
                <td>删除权限</td>
                <td>操作权限</td>
                </thead>
                <tbody>
                <tr>
                    <td>首页</td>
                    <td><input type="checkbox" name="child[]" value="site/index"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php foreach($data as $k=>$v){
                    if($v['is_display']==1 && $v['name']!='stocks/index'){
                    ?>
                <tr>
                    <td><?=$v['menu_name']?></td>
                        <td><input type="checkbox" name="child[]"  value="<?=$v['name']?>"/></td>
                  <?}elseif($v['is_display']==1 && $v['name']=='stocks/index'){?>
                        <tr>
                            <td><?=$v['menu_name']?></td>
                            <td><input type="checkbox" name="child[]"  value="<?=$v['name']?>"></td>
                        </tr>
                    <?php }else{?>
                        <?php if($v['name']=='user/profile'){?>
                            <input type="hidden" name="child[]"  value="<?=$v['name']?>">
                        <?}else{?>
                            <?php if($v['name']=='cstocks/handle' || $v['name']=='refuse/handle' || $v['name']=='sale/handle'){?>
                               <td></td><td></td><td></td><td><input type="checkbox" name="child[]"  value="<?=$v['name']?>"></td>
							<?php }else{?>
                            <td><input type="checkbox" name="child[]"  value="<?=$v['name']?>"></td>
                            <?php } ?>
                        <?php }?>


                 <?php }if(($k+1)%5==0 && ($v['parent']==5 || $v['parent']==3)){?>
                        </tr>
                    <?php }?>
                <?php }?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="orders-newbut">
        <?=Html::submitButton("保存")?>
        <a href="<?=\yii\helpers\Url::to(['user/index'])?>">
            <span class="orders-newbut2">返回</span>
        </a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php if(Yii::$app->user->identity->store_id==0){?>
<?php \frontend\components\JsBlock::begin()?>
<script type="text/javascript">


    window.onload = function(){
        var oBtn01 = document.getElementById('jurisdiction01');
        var oBtn02 = document.getElementById('jurisdiction02');
        var oBox = document.getElementById('jurisdiction_box');
        oBtn01.onclick = function(){
                oBox.style.display = "block";
        };
        oBtn02.onclick = function(){
                oBox.style.display = "none";
        }
    }

</script>
<?php \frontend\components\JsBlock::end()?>
<?php }?>


