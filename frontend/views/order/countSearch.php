<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\PurchaseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['count'],
    'method' => 'get',
]); ?>

<div class="seeks clearfix">
    <div class="close_btn"><input type="text" name="warehouse_name" placeholder="请直接选择或输入选择仓库" value="<?=Yii::$app->request->get('warehouse_name')?>"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label><?php if(strtotime(Yii::$app->request->get('create_time_start'))>strtotime(Yii::$app->request->get('create_time_end'))){?>&nbsp;&nbsp;<strong style="color: red; margin: 0; background: none;">开始时间不能大于结束时间</strong><?php };?></p>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/count')){?>
        <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
    <?php };?>

</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs seeks-boxst2 clearfix">
        <p>创建时间</p>
        <input type="text" placeholder="创建开始时间" name="create_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('create_time_start')?>"/>
        <span>-</span>
        <input type="text" placeholder="创建终止时间" name="create_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('create_time_end')?Yii::$app->request->get('create_time_end'):date('Y-m-d', time())?>"/>
    </div>
    <div class="seeks-boxs clearfix">
        <p>订单编号</p>
        <input type="text" name="order_no" value="<?=Yii::$app->request->get('order_no')?>" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>收货人</p>
        <input type="text" name="accept_name" value="<?=Yii::$app->request->get('accept_name')?>" />
    </div>
    <?php
    if(Yii::$app->user->identity->username=='admin'){
        //获取入驻商家
        $store_list = \frontend\components\Search::SearchStore();
        ?>
        <div class="seeks-boxs clearfix">
            <p>入驻商家</p>
            <select name="store_id">
                <option <?php if(''==Yii::$app->request->get('store_id')){echo 'selected';};?> value=''>请选择</option>
                <?php foreach($store_list as $value){?>
                    <option <?php if($value['store_id']==Yii::$app->request->get('store_id')){echo 'selected';};?> value="<?=$value['store_id']?>"><?=$value['name']?></option>
                <?php }?>
            </select>
        </div>
    <?php }?>
</div>
<?php ActiveForm::end(); ?>
