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
$principal = $query->select('user_id,username')->from(Yii::$app->getDb()->tablePrefix.'user')->where('status=1'.$s_store_id)->all();

//销售平台
$shop = $query->select('shop_id,name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('status=1'.$s_store_id)->all();

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="seeks clearfix">
    <div class="close_btn"><input type="text" name="order_no" placeholder="请直接选择或输入选择订单编号" value="<?=Yii::$app->request->get('order_no')?>"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label><?php if(strtotime(Yii::$app->request->get('sale_time_start'))>strtotime(Yii::$app->request->get('sale_time_end'))){?>&nbsp;&nbsp;<strong style="color: red; margin: 0; background: none;">开始时间不能大于结束时间</strong><?php };?></p>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/create')){?><a href="<?=Url::to(['order/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建订单</span></a><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/view')){?>
        <span><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
    <?php }?>
</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs clearfix">
        <p>仓库</p>
        <select name="warehouse_id">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select>
    </div>
    <div class="seeks-boxs clearfix">
        <p>销售平台</p>
        <select name="shop_id">
            <option value="">请选择</option>
            <?php foreach($shop as $v){?>
                <option value="<?php echo $v['shop_id'];?>" <?php if($v['shop_id']==Yii::$app->request->get('shop_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select>
    </div>
    <div class="seeks-boxs clearfix">
        <p>商品名称</p>
        <input type="text" name="goods_name" value="<?=Yii::$app->request->get('goods_name')?>" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>出库状态</p>
        <select name="delivery_status">
            <option <?php if(Yii::$app->request->get('delivery_status')==''){echo 'selected';};?> value="">请选择</option>
            <option <?php if(Yii::$app->request->get('delivery_status')==2){echo 'selected';};?> value="2">未出库</option>
            <option <?php if(Yii::$app->request->get('delivery_status')==1){echo 'selected';};?> value="1">已出库</option>
        </select>
    </div>

    <div class="seeks-boxs seeks-boxst1 clearfix">
        <p>物流单号</p>
        <input type="text" name="delivery_code" value="<?=Yii::$app->request->get('delivery_code')?>" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>客户帐号</p>
        <input type="text" name="customer_name" value="<?=Yii::$app->request->get('customer_name')?>" />
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
    <div class="seeks-boxs seeks-boxst2 clearfix">
        <p>销售日期</p>
        <input type="text" placeholder="销售开始时间" name="sale_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('sale_time_start')?>"/>
        <span>-</span>
        <input type="text" placeholder="销售终止时间" name="sale_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('sale_time_end')?Yii::$app->request->get('sale_time_end'):date('Y-m-d', time())?>"/>
    </div>
</div>
<?php ActiveForm::end(); ?>
