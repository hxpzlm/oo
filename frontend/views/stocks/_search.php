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
?>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" id="goods_name"  name="goods_name" placeholder="请输入商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>" autocomplete="off"/>

        <input type="hidden" name="store_id" value="<?=Yii::$app->user->identity->store_id?>">
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'stocks/index')){?><span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span><?php } ?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>品牌名称</p>
            <input type="text" name="brand_name"placeholder="请输入品牌名" <?=Yii::$app->request->get('brand_name')?> autocomplete="off"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>条形码</p>
            <input type="text" name="barode_code" placeholder="请输入商品条形码" value="<?=Yii::$app->request->get('barode_code')?>"/>
        </div>
        <div class="seeks-boxs  clearfix">
            <p>仓库:</p>
            <select name="warehouse_id">
                <option value="">请选择</option>
                <?php foreach($warehose as $v){?>
                    <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

