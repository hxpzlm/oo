<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 17:16
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->title = $model['username'].'用户详情-用户权限';
$data = frontend\components\menuHelper::getAuthData();
$wh['status'] = 1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id'] = Yii::$app->user->identity->store_id;
}
$store = \frontend\models\Store::findAll($wh);
?>
<div class="container">
    <?php $form = ActiveForm::begin(['id' => 'form-update']); ?>
    <h4 class="orders-newtade">账号信息</h4>
    <div class="orders-new clearfix">
        <p>账号:</p>
        <?= $form->field($model, 'username')->textInput(['autofocus' => true,'disabled'=>"disabled"])->label(false)?>
    </div>
    <div class="orders-new clearfix">
        <p>入驻商家:</p>
        <select name="User[store_id]" disabled="disabled">
            <?php foreach ($store as $item) {?>
                <option value="<?=$item['store_id']?>" <?=($model['store_id']==$item['store_id']?"selected='selected'":"")?>><?=$item['name']?></option>
            <?php }?>
        </select>
    </div>
    <div class="orders-new clearfix">
        <p>状态:</p>
        <div class="jurisdiction_top">
            <input type="radio" name="User[status]" value="1" <?=($model['status']==1)?"checked='checked'":""?> disabled="disabled"/><label>正常</label>
            <input type="radio" name="User[status]" value="0" <?=($model['status']==0)?"checked='checked'":""?> disabled="disabled"/><label>停用</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>顺序:</p>
        <input type="text" name="User[sort]" value="<?=$model['sort']?>" disabled="disabled"/>
    </div>
    <h4 class="orders-newtade">用户信息</h4>
    <div class="orders-new clearfix">
        <p>姓名:</p>
        <input type="text" name="User[real_name]" value="<?=$model['real_name']?>" disabled="disabled"/>
    </div>
    <div class="orders-new clearfix">
        <p>性别:</p>
        <div class="jurisdiction_top">
            <input type="radio" name="User[sex]" value="1" <?=($model['sex']==1)?"checked='checked'":""?> disabled="disabled"/><label>男</label>
            <input type="radio" name="User[sex]" value="0" <?=($model['sex']==0)?"checked='checked'":""?> disabled="disabled"/><label>女</label>
        </div>
    </div>
    <div class="orders-new clearfix">
        <p>电话:</p>
        <input type="text" name="User[mobile]" value="<?=$model['mobile']?>" disabled="disabled"/>
    </div>
    <div class="orders-new clearfix">
        <p>邮箱:</p>
        <input type="text" name="User[email]" value="<?=$model['email']?>" disabled="disabled"/>
    </div>
    <h4 class="orders-newtade">权限信息</h4>
    <div class="orders-new clearfix">
        <p class="orders-newt1 jurisdiction_left">账号权限:</p>
        <div class="jurisdiction">
            <div class="jurisdiction_top">
                <input type="radio" name="User[type]" value="1" <?=($model['type']==1)?"checked='checked'":""?>  disabled="disabled"/><label>系统管理员</label>
                <input type="radio" name="User[type]" value="2" <?=($model['type']==2)?"checked='checked'":""?>  disabled="disabled"/><label>普通管理员</label>
            </div>
            <div class="clearfix"></div>
            <table class="table01">
                <thead><td></td></thead>
                <tbody>
                <tr><td>首页</td></tr>
                <tr style="height: 54px; line-height: 54px"><td>采购</td></tr>
                <tr style="height: 135px; line-height: 135px"><td>销售</td></tr>
                <tr style="height: 162px; line-height: 162px"><td>库存</td></tr>
                <tr style="height: 108px; line-height: 108px"><td>商品</td></tr>
                <tr style="height: 54px; line-height: 54px"><td>系统设置</td></tr>
                </tbody>
            </table>
            <table class="table02">
                <thead>
                <td> </td>
                <td>查看权限</td>
                <td>编辑权限</td>
                <td>新建权限</td>
                <td>删除权限</td>
                </thead>
                <tbody>
                <tr>
                    <td>首页</td>
                    <td><input type="checkbox" name="child[]" value="site/index" <?php if(Yii::$app->authManager->checkAccess($model['user_id'],"site/index")){?>checked="checked"<?php }?> disabled="disabled"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php foreach($data as $k=>$v){
                    if($v['is_display']==1 && $v['name']!='stocks/index'){
                        ?>
                        <tr>
                        <td><?=$v['menu_name']?> <input type="hidden" name="child[]"  value="<?=$v['name']?>"/></td>
                    <?}elseif($v['is_display']==1 && $v['name']=='stocks/index'){?>
                        <tr>
                            <td><?=$v['menu_name']?></td>
                            <td><input type="checkbox" name="child[]"  value="<?=$v['name']?>" <?php if(Yii::$app->authManager->checkAccess($model['user_id'],$v['name'])){?>checked="checked"<?php }?> disabled="disabled"></td>
                        </tr>
                    <?php }else{?>
                        <?php if($v['name']=='user/profile'){?>
                            <input type="hidden" name="child[]"  value="<?=$v['name']?>">
                        <?}else{?>
                            <td><input type="checkbox" name="child[]"  value="<?=$v['name']?>" <?php if(Yii::$app->authManager->checkAccess($model['user_id'],$v['name'])){?>checked="checked"<?php }?> disabled="disabled"></td>
                        <?php }?>


                    <?php }if(($k+1)%5==0 && ($v['parent']==5 || $v['parent']==3)){?>
                        </tr>
                    <?php }?>
                <?php }?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="orders-newbut">
        <a href="<?=\yii\helpers\Url::to(['user/index'])?>">
            <button class="orders-newbut1" type="button">返回</button>
        </a>
    </div>
    <?php ActiveForm::end(); ?>
</div>