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
    $w_store_id = 'store_id='.$userinfo['store_id'];
}else{
    $s_store_id = '';
    $w_store_id = '';
}

$query = new \yii\db\Query();

//获取仓库
$warehose = $query->select('warehouse_id,name')->from(Yii::$app->getDb()->tablePrefix.'warehouse')->where('status=1'.$s_store_id)->all();

//销售平台
$shop = $query->select('shop_id,name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('status=1'.$s_store_id)->all();

//获取客户帐号
$customer = $query->select('customers_id,username')->from(Yii::$app->getDb()->tablePrefix.'customers')->where($w_store_id)->all();

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="seeks clearfix">
    <input type="text" name="order_no" placeholder="请直接选择或输入选择订单编号" value="<?=Yii::$app->request->get('order_no')?>"/>
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label></p>
    <?php if(strtotime(Yii::$app->request->get('buy_time_1'))>strtotime(Yii::$app->request->get('buy_time_2'))){?><span class="warning">开始时间不能大于结束时间</span><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'refuse-order/create')){?><a href="<?=Url::to(['refuse-order/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建订单</span></a><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'refuse-order/view')){?>
        <span onclick="javascript:location.href='<?=Url::to(['refuse-order/index','action' => 'export'])?>'"><i class="iconfont">&#xe60a;</i></I>导出表格</span>
    <?php }?>
</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs clearfix">
        <p>仓库</p>
        <select name="warehouse_id">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_name')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select>
    </div>
    <div class="seeks-boxs clearfix">
        <p>销售平台</p>
        <select name="shop_id">
            <option value="">请选择</option>
            <?php foreach($shop as $v){?>
                <option value="<?php echo $v['shop_id'];?>" <?php if($v['shop_id']==Yii::$app->request->get('name')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select>
    </div>

    <div class="seeks-boxs seeks-boxst1 clearfix">
        <p>入库状态</p>
        <select name="status">
            <option value="">请选择</option>
            <option value="0" <?php if(Yii::$app->request->get('status')==0){echo 'selected';};?>>否</option>
            <option value="1" <?php if(Yii::$app->request->get('status')==1){echo 'selected';};?>>是</option>
        </select>
    </div>
    <div class="seeks-boxs clearfix">
        <p>客户帐号</p>
        <select name="customer_id">
            <option value="">请选择</option>
            <?php foreach($customer as $v){?>
                <option value="<?php echo $v['customers_id'];?>" <?php if($v['customers_id']==Yii::$app->request->get('customers_id')){echo 'selected';};?>><?php echo $v['username'];?></option>
            <?php };?>
        </select>
    </div>
    <div class="seeks-boxs seeks-boxst2 clearfix">
        <p>退货时间</p>
        <input type="text" placeholder="退货开始时间" name="refuse_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('refuse_time_start')?>"/>
        <span>-</span>
        <input type="text" placeholder="退货终止时间" name="refuse_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('refuse_time_end')?>"/>
    </div>
</div>
<?php ActiveForm::end(); ?>
