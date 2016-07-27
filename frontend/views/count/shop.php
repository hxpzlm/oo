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
?>

<!--主体内容s-->

<div class="stati_mian">
    <?php $form = ActiveForm::begin([
        'action' => ['shop'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input id="shop_name" type="text" name="shop_name" placeholder="请直接选择或输入选择销售平台" value="<?=Yii::$app->request->get('shop_name')?>" autocomplete="off"/>
         <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/shop')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>日期</p>
            <input type="text" name="sale_time_start" class="start_rl" placeholder="请选择开始日期"  value="<?=Yii::$app->request->get('sale_time_start')?>"/>-
            <input type="text" name="sale_time_end" class="end_rl" placeholder="请选择结束日期"  value="<?=Yii::$app->request->get('sale_time_end')?Yii::$app->request->get('sale_time_end'):date('Y-m-d', time())?>" />
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
    </div>
    <?php ActiveForm::end(); ?>
    <!--中间内容部分-->
    <div class="cengar_cnt">
        <div class="one_cr">
            <div class="nr_left nr_bj" id="nr1">
            </div>
            <div class="nr_left" id="nr2">
            </div>
        </div>

        <table class="table_b" border="0" cellspacing="0" cellpadding="0">
            <thead>
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
            </thead>
            <tbody>
            <?php foreach ($model as $v){?>
                <tr>
                    <td><?=$v['shop_name']?></td>
                    <td><?=$v['sale_nums']?></td>
                    <td><?=$v['amount']?></td>
                    <td><?=round($v['amount']/$v['c_nums'],2)?></td>
                    <td><?=round(($v['amount']/$count)*100,2)?>%</td>
                    <td><?=empty($v['refuse_nums'])?0:$v['refuse_nums']?></td>
                    <td><?=empty($v['r_amount'])?'0.00':$v['r_amount']?></td>
                    <td><?=empty($r_count)?'0':round(($v['r_amount']/$r_count)*100,2)?>%</td>
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
         $('#shop_name').autocomplete({
             minLength:0,
             source: [
               <?php foreach (\frontend\models\Shop::findAll($where) as $v){?>
                 "<?=$v['name']?>",
               <?php } ?>
             ]
         });
        $('#shop_name').focus(function(){
            if($(this).val() == ""){
                $('#shop_name').autocomplete("search", "");
            }
        });


        $('#nr1').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '销售平台销售金额及占比,<?=date('Y',time())?>'
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
                name: '销售金额及占比',
                data: [
                    <?php foreach ($model as $v){?>
                    ['<?=$v['shop_name']?>',   <?=round(($v['amount']/$count)*100,2)?>],
                    <?php } ?>
                ]
            }]
        });

        $('#nr2').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '销售平台退货金额及占比,<?=date('Y',time())?>'
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
                name: '退货金额及占比',
                data: [
                    <?php foreach ($model as $v){?>
                    ['<?=$v['shop_name']?>', <?=empty($r_count)?'0':round(($v['r_amount']/$r_count)*100,2)?>],
                    <?php } ?>
                ]
            }]
        });
    });
</script>
<?php \frontend\components\JsBlock::end()?>
