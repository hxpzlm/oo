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
//商品品牌
$brand = $query->select('brand_id,name')->from(Yii::$app->getDb()->tablePrefix.'brand')->where('status=1'.$s_store_id)->all();
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="seeks clearfix">
    <input type="text" name="goods_name" placeholder="请直接选择或输入选择商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>"/>
    <input type="hidden" name="goods_id" value="" />
    <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
    <p class="seeks-xl">更多搜索条件<label>▼</label></p>
    <?php if(strtotime(Yii::$app->request->get('update_time_start'))>strtotime(Yii::$app->request->get('update_time_end'))){?><span class="warning">开始时间不能大于结束时间</span><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/create')){?><a href="<?=Url::to(['moving/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建调剂</span></a><?php };?>
    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/view')){?>
        <span onclick="javascript:location.href='<?=Url::to(['moving/index','action' => 'export'])?>'"><i class="iconfont">&#xe60a;</i></I>导出表格</span>
    <?php }?>
</div>
<div class="seeks-box clearfix">
    <div class="seeks-boxs clearfix">
        <p>条形码</p>
        <input type="text" name="barode_code" value="" />
    </div>
    <div class="seeks-boxs clearfix">
        <p>商品品牌</p>
        <input type="text" name="brand_name" value="" />
        <input type="hidden" name="brand_id" value="" />
    </div>
    <div class="seeks-boxs seeks-boxst1 clearfix">
        <p>仓库</p>
        <select name="from_warehouse_id">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('from_warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
        </select><p style="width: 21px">&nbsp;-></p><select name="to_warehouse_id">
            <option value="">请选择</option>
            <?php foreach($warehose as $v){?>
                <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('to_warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
            <?php };?>
            </select>
    </div>
    <div class="seeks-boxs seeks-boxst2 clearfix">
        <p>调剂日期</p>
        <input type="text" placeholder="调剂开始时间" name="update_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('update_time_start')?>"/>
        <span>-</span>
        <input type="text" placeholder="调剂终止时间" name="update_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('update_time_end')?>"/>
    </div>
</div>
<?php ActiveForm::end(); ?>