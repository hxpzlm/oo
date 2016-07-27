<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RefuseSearch */
/* @var $form yii\widgets\ActiveForm */
$query = new \yii\db\Query();
if(Yii::$app->user->identity->store_id>0){
    $store_id=" and store_id=".Yii::$app->user->identity->store_id;
}else{
    $store_id="";
}
$warehose = $query->select('warehouse_id,name')->from('s2_warehouse')->where('status=1 '.$store_id)->all();
 $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>
<div class="seeks clearfix">
    <div class="close_btn"><input type="text" id="goods_name" name="goods_name" placeholder="请输入商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>" autocomplete="off"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
    <input type="hidden" name="store_id" value="<?=Yii::$app->user->identity->store_id?>">
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label></p>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'stocks/index')){?><span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span><?php } ?>
</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs clearfix">
        <p>供应商</p>
        <input type="text" placeholder="请输入供应商" name="supplier_name"  value="<?=Yii::$app->request->get('supplier_name')?>" autocomplete="off"/>
    </div>
    <div class="seeks-boxs clearfix">
        <p>品牌名称</p>
        <input type="text" placeholder="请输入商品品牌名" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>" autocomplete="off"/>
    </div>
    <div class="seeks-boxs clearfix">
        <p>条形码</p>
        <input type="text" placeholder="请输入条形码" name="barode_code"  value="<?=Yii::$app->request->get('barode_code')?>"/>
    </div>
    <div class="seeks-boxs seeks-boxst1 clearfix">
        <p>仓库</p>
        <select name="warehouse_id">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select>
    </div>
    <div class="seeks-boxs clearfix">
        <p>是否入库</p>
        <select name="purchases_status">
		    <?php $status =Yii::$app->request->get('purchases_status');?>
            <option value="" <?=empty($status)?'selected="selected"':''?>>请选择</option>
            <option value="1" <?=($status==1)?'selected="selected"':''?>>是</option>
            <option value="2" <?=($status==2)?'selected="selected"':''?>>否</option>
        </select>
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
    <div class="seeks-boxs seeks-boxst2 clearfix">
        <p>采购日期</p>
        <input type="text" placeholder="采购开始日期" name="buy_time_start" id='start-date' class="laydate-icon"  value="<?=Yii::$app->request->get('buy_time_start')?>" />
        <span>-</span>
        <input type="text" placeholder="采购终止日期" name="buy_time_end" id='end-date' class="laydate-icon"  value="<?=Yii::$app->request->get('buy_time_end')?>"/>
    </div>
</div>
<?php ActiveForm::end(); ?>


