<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\PurchaseSearch */
/* @var $form yii\widgets\ActiveForm */

$user = new User();
$userinfo = $user->findIdentity(Yii::$app->session['__id']);
if(!empty($userinfo['store_id'])){
    $s_store_id = ' and store_id='.$userinfo['store_id'];
}else{
    $s_store_id = '';
}
$query = new \yii\db\Query();
//获取仓库
$warehose = $query->select('warehouse_id,name')->from(Yii::$app->getDb()->tablePrefix.'warehouse')->where('status=1'.$s_store_id)->all();
//获取负责人
$principal = $query->select('user_id,real_name')->from(Yii::$app->getDb()->tablePrefix.'user')->where('status=1'.$s_store_id)->all();

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="seeks clearfix">
    <div class="close_btn"><input type="text" name="goods_name" placeholder="请直接选择或输入选择商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label><?php if(strtotime(Yii::$app->request->get('buy_time_start'))>strtotime(Yii::$app->request->get('buy_time_end'))){?>&nbsp;&nbsp;<strong style="color: red; margin: 0; background: none;">开始时间不能大于结束时间</strong><?php };?></p>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'purchase/create')){?><a href="<?=Url::to(['purchase/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建采购</span></a><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'purchase/view')){?>
        <span><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
    <?php }?>
</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs clearfix">
        <p>供应商</p>
        <input type="text" name="supplier_name" value="<?=Yii::$app->request->get('supplier_name')?>" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>商品品牌</p>
        <input type="text" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>条形码</p>
        <input type="text" name="barode_code" value="<?=Yii::$app->request->get('barode_code')?>" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>负责人</p>
        <select name="principal_id">
            <option value="">请选择</option>
            <?php foreach($principal as $v){?>
                <option value="<?php echo $v['user_id'];?>" <?php if($v['user_id']==Yii::$app->request->get('principal_id')){echo 'selected';};?>><?php echo $v['real_name'];?></option>
            <?php };?>
        </select>
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
        <p>入库状态</p>
        <select name="purchases_status">
            <option <?php if(''==Yii::$app->request->get('purchases_status')){echo 'selected';};?> value=''>请选择</option>
            <option <?php if(2==Yii::$app->request->get('purchases_status')){echo 'selected';};?> value="2">未入库</option>
            <option <?php if(1==Yii::$app->request->get('purchases_status')){echo 'selected';};?> value="1">已入库</option>
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
        <input type="text" placeholder="采购开始日期" name="buy_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('buy_time_start')?>"/>
        <span>-</span>
        <input type="text" placeholder="采购终止日期" name="buy_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('buy_time_end')?Yii::$app->request->get('buy_time_end'):date('Y-m-d', time())?>"/>
    </div>
</div>
<?php ActiveForm::end(); ?>
