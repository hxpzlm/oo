<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\PurchaseSearch */
/* @var $form yii\widgets\ActiveForm */

//收货地址
$address = (new \yii\db\Query())->select('address_id,accept_name')->from(Yii::$app->getDb()->tablePrefix.'address')->all();
?>

<?php $form = ActiveForm::begin([
    'action' => ['count'],
    'method' => 'get',
]); ?>

<div class="seeks clearfix">
    <input type="text" name="warehouse_name" placeholder="请直接选择或输入选择仓库" value="<?=Yii::$app->request->get('warehouse_name')?>"/>
    <?= Html::activeHiddenInput($model,'warehouse_id')?>
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label></p>
    <?php if(strtotime(Yii::$app->request->get('create_time_start'))>strtotime(Yii::$app->request->get('create_time_end'))){?><span class="warning">开始时间不能大于结束时间</span><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/count')){?><a href="<?=Url::to(['order/count','action' => 'export'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>导出表格</span></a><?php };?>

</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs seeks-boxst2 clearfix">
        <p>创建时间</p>
        <input type="text" placeholder="创建开始时间" name="create_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('create_time_start')?>"/>
        <span>-</span>
        <input type="text" placeholder="创建终止时间" name="create_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('create_time_end')?>"/>
    </div>
    <div class="seeks-boxs clearfix">
        <p>订单编号</p>
        <input type="text" name="order_no" value="" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>收货人</p>
        <select id="ordersearch-address_id" name="OrderSearch[address_id]">
            <option value="">请选择</option>
            <?php foreach($address as $v){?>
                <option value="<?=$v['address_id']?>"><?=$v['accept_name']?></option>
            <?php }?>
        </select>
    </div>
</div>
<?php ActiveForm::end(); ?>
