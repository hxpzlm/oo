<?php

/* @var $this yii\web\View */

$this->title = '销售平台业务统计';
use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/statistics.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/statistics.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/highcharts.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/grid.js',['depends'=>['yii\web\YiiAsset']]);
$where['status']=1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id']=Yii::$app->user->identity->store_id;
}else{
    if(!empty(Yii::$app->request->get('store_id')))
        $wh['store_id']=Yii::$app->request->get('store_id');
}
$ware = \frontend\models\WarehouseModel::findAll($where);
?>

<!--主体内容s-->

<div class="stati_mian">
    <?php $form = ActiveForm::begin([
        'action' => ['delivery'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input id="delivery_name" type="text" name="delivery_name" placeholder="请直接选择或输入选择物流公司名称" value="<?=Yii::$app->request->get('delivery_name')?>" autocomplete="off"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/shop')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>仓库</p>
            <select name="warehouse_id">
                <option value="">请选择</option>
                <?php foreach($ware as $v){?>
                    <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
        <?php if(Yii::$app->user->identity->store_id==0){
            $store = \frontend\models\Store::findAll(['status'=>1]);
            ?>
            <div class="seeks-boxs clearfix">
                <p>入驻商家</p>
                <select name="store_id">
                    <option value="">请选择</option>
                    <?php foreach($store as $v){?>
                        <option value="<?php echo $v['store_id'];?>" <?php if($v['store_id']==Yii::$app->request->get('store_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                    <?php };?>
                </select>
            </div>
        <?php } ?>
        <div class="seeks-boxs clearfix">
            <p>日期</p>
            <input type="text" name="time_start" class="start_rl" placeholder="请选择开始日期"  value="<?=Yii::$app->request->get('time_start')?>"/>-
            <input type="text" name="time_end" class="end_rl" placeholder="请选择结束日期"  value="<?=Yii::$app->request->get('time_end')?Yii::$app->request->get('time_end'):date('Y-m-d', time())?>" />
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <!--中间内容部分-->
    <div class="cengar_cnt">
        <div class="one_cr">
            <div class="nr_bj" id="nr1">
            </div>
        </div>

        <table class="table_b" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th>物流公司</th>
                <th>发单量(笔)</th>
                <th>占比</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $v){?>
                <tr>
                    <td><?=$v['delivery_name']?></td>
                    <td><?=$v['totle']?></td>
                    <td><?=empty($v['totle'])?'0':round(($v['totle']/$totle)*100,2)?>%</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php echo \frontend\components\PageWidget::widget([
            'pagination' => $pagination,
        ]);;?>
        <p class="p_centent">注：1、客单价=某销售平台销售收入/该销售平台购买客户数；2、销售占比=该销售平台的销售金额/所有销售平台的销售总额*100%；
            3、退货占比=该销售平台的退货金额/所有销售平台的退货总额*100%。数据精确到小数点后两位。</p>
    </div>
</div>
<!--主体内容e-->
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function () {
        $('#delivery_name').autocomplete({
            minLength:0,
            source: [
                <?php foreach (\frontend\models\Expressway::findAll($where) as $v){?>
                "<?=$v['name']?>",
                <?php } ?>
            ]
        });
        $('#delivery_name').focus(function(){
            if($(this).val() == ""){
                $('#delivery_name').autocomplete("search", "");
            }
        });
        $('#nr1').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '物流公司发单量及占比,<?=date('Y',time())?>'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        format: '<b>{point.name}</b>: {point.percentage:.2f} %'
                    }
                }
            },
            series: [{
                type: 'pie',
                name: '发单量占比',
                data: [
                    <?php foreach ($model as $v){?>
                    ['<?=$v['delivery_name']?>',   <?=round(($v['totle']/$totle)*100,2)?>],
                    <?php } ?>
                ]
            }]
        });

    });
</script>
<?php \frontend\components\JsBlock::end()?>
