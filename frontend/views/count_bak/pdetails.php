<?php

/* @var $this yii\web\View */

$this->title = '进销存明细统计';
use yii\helpers\Html;

use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
    laydate({elem: '#start-date'});//绑定元素
    laydate({elem: '#end-date'});
}();", \yii\web\View::POS_END);
$wh['status']=1;
if(Yii::$app->user->identity->store_id>0) $wh['store_id']=Yii::$app->user->identity->store_id;
$ware = \frontend\models\WarehouseModel::findAll($wh);
$brand = \frontend\models\Brand::findAll($wh);
$totle=0;
?>
<!--内容-->
<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['pdetails'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" name="good_name" placeholder="请直接选择或输入商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/pdetails')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>商品品牌</p>
            <input type="text" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>" />
        </div>
        <div class="seeks-boxs seeks-boxst2 clearfix">
            <p>时间</p>
            <input type="text" placeholder="时间" name="time" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('time')?>"/>
            </div>
    </div>
    <?php ActiveForm::end(); ?>
    <table class="orders-info">
        <tr>
            <th>品牌</th>
            <th>商品中英文名称</th>
            <th>规格</th>
            <th>单位</th>
            <?php foreach ($ware as $v){
           ?>
            <th><?=$v['name']?></th>
            <?php } ?>
            <th>数量小计</th>
            <th>采购单价</th>
            <th>采购总额</th>
        </tr>
        <?php foreach ($model as $val){?>
        <tr>
            <td><?=$val['brand_name']?></td>
            <td><?=$val['goods_name']?></td>
            <td><?=$val['spec']?></td>
            <td><?=$val['unit_name']?></td>
            <?php foreach ($ware as $v){
                $row = (new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'p_s')->select('stock_num')->where(['warehouse_id'=>$v['warehouse_id'],'goods_id'=>$val['goods_id'],'month'=>$time-1])->one();
                $stock_num=empty($row['stock_num'])?"0":$row['stock_num'];
                $totle=$totle+$stock_num;
                ?>
            <td><?=$stock_num?></td>
            <?php } ?>
            <td><?=$totle?></td>
        </tr>
        <?php $totle=0;} ?>
    </table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
