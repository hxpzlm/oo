<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RefuseSearch */
/* @var $form yii\widgets\ActiveForm */
$query = new \yii\db\Query();
$store_id = Yii::$app->user->identity->store_id ?  : "";
if(Yii::$app->user->identity->store_id>0){
    $store_id=" and store_id=".Yii::$app->user->identity->store_id;
}
$warehose = $query->select('warehouse_id,name')->from('s2_warehouse')->where('status=1 '.$store_id)->all();
//获取负责人
$shop = $query->select('shop_id,name')->from('s2_shop')->where('status=1 '.$store_id)->all();
?>
<div class="refuse-model-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" name="order_no" placeholder="请输入订单编号" value="<?=Yii::$app->request->get('order_no')?>" autocomplete="off"/>
        <input type="hidden" name="store_id" value="<?=Yii::$app->user->identity->store_id?>">
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'refuse/index')){?><span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl();?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span><?php } ?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs  clearfix">
            <p>仓库:</p>
            <select name="warehouse_id">
                <option value="">请选择</option>
                <?php foreach($warehose as $v){?>
                    <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
        <div class="seeks-boxs clearfix">
            <p>销售平台:</p>
            <select name="shop">
                <option value="">请选择</option>
                <?php foreach($shop as $v){?>
                    <option value="<?php echo $v['shop_id'];?>" <?php if($v['shop_id']==Yii::$app->request->get('shop')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
        <div class="seeks-boxs seeks-boxst1 clearfix">
            <p>客户姓名:</p>
            <input type="text" name="customer_name" placeholder="请输入客户姓名" value="<?=Yii::$app->request->get('customer_name')?>"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>入库状态:</p>
            <select name="status">
                 <?php $status =Yii::$app->request->get('status');?>
            <option value="" <?=empty($status)?'selected="selected"':''?>>请选择</option>
            <option value="1" <?=($status==1)?'selected="selected"':''?>>是</option>
            <option value="2" <?=($status==2)?'selected="selected"':''?>>否</option>
            </select>
        </div>
        <div class="seeks-boxs seeks-boxst2 clearfix">
            <p>退货日期:</p>
            <input type="text" name="refuse_time_start"  id='start-date' class="laydate-icon"/>
            <span>-</span>
            <input type="text" name="refuse_time_end" id='end-date' class="laydate-icon"/>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
