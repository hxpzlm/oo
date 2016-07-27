<?php

/* @var $this yii\web\View */

$this->title = '销售平台业务统计';
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
?>
<!--内容-->
<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['shop'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" name="shop_name" placeholder="请直接选择或输入选择销售平台" value="<?=Yii::$app->request->get('shop_name')?>"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/shop')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs seeks-boxst2 clearfix">
            <p>销售时间</p>
            <input type="text" placeholder="销售开始时间" name="sale_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('sale_time_start')?>"/>
            <span>-</span>
            <input type="text" placeholder="销售终止时间" name="sale_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('sale_time_end')?Yii::$app->request->get('sale_time_end'):date('Y-m-d', time())?>"/>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <table class="orders-info">
        <tr>
            <th>销售平台</th>
            <th>销售笔数(笔)</th>
            <th>销售金额(元)</th>
            <th>客单价(元)</th>
            <th>销售占比</th>
            <th>退货笔数(笔)</th>
            <th>退货金额(元)</th>
            <th>退货占比</th>
        </tr>
        <?php foreach ($model as $v){?>
        <tr>
            <td><?=$v['shop_name']?></td>
            <td><?=$v['sale_nums']?></td>
            <td><?=$v['amount']?></td>
            <td><?=round($v['amount']/$v['sale_nums'],2)?></td>
            <td><?=round(($v['amount']/$count)*100,2)?>%</td>
            <td><?=empty($v['refuse_nums'])?"0":$v['refuse_nums']?></td>
            <td><?=empty($v['r_amount'])?"0":$v['r_amount']?></td>
            <td><?=($r_count>0)?round(($v['r_amount']/$r_count)*100,2):"0"?>%</td>
        </tr>
        <?php } ?>
    </table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
