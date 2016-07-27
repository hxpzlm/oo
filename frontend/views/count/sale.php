<?php

/* @var $this yii\web\View */

$this->title = '商品销售统计';
use yii\helpers\Html;

use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/statistics.js',['depends'=>['yii\web\YiiAsset']]);

$wh['status']=1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id']=Yii::$app->user->identity->store_id;
}else{
    if(!empty(Yii::$app->request->get('store_id')))
    $wh['store_id']=Yii::$app->request->get('store_id');
}
$ware = \frontend\models\WarehouseModel::findAll($wh);
$brand = \frontend\models\Brand::findAll($wh);
?>
<!--内容-->
<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['sale'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" id="goods_name" name="goods_name" placeholder="请直接选择或输入商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>" autocomplete="off"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/erp')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>品牌</p>
            <input type="text" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>"  autocomplete="off"/>
        </div>
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
    <table class="orders-info">
        <tr>
            <th>商品中英文名称</th>
            <th>规格</th>
            <th>品牌</th>
            <th>单位</th>
            <th>销售单价(元)</th>
            <th>销售数量</th>
            <th>销售金额(元)</th>
            <th>销售占比</th>

        </tr>
        <?php foreach ($model as $v){?>
            <tr>
                <td class="table-left">　　<?=$v['goods_name']?></td>
                <td><?=$v['spec']?></td>
                <td><?=$v['brand_name']?></td>
                <td><?=$v['unit_name']?></td>
                <td><?=round($v['amount']/$v['number'],2)?></td>
                <td><?=$v['number']?></td>
                <td><?=$v['amount']?></td>
                <td><?=empty($v['amount'])?'0.00':round(($v['amount']/$totle)*100,2)?>%</td>
            </tr>
        <?php } ?>
    </table>
    <p class="p_centent">注：1、销售单价=该商品所有销售金额之和/销售总量（如果有搜索条件则增加统计约束）；

        2、销售金额=该商品所有销售金额之和；

        3、销售占比=本商品销售金额/所有商品销售金额*100%（如果有搜索条件则增加统计约束）。
    </p>
    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
        $('#goods_name').autocomplete({
            minLength:0,
            source: [
                <?php foreach (\frontend\components\Search::SearchGoods() as $v){?>
                "<?=$v['name']?>",
                <?php } ?>
            ]
        });
        $('#goods_name').focus(function(){
            if($(this).val() == ""){
                $('#goods_name').autocomplete("search", "");
            }
        });

        $("input[name='brand_name']").autocomplete({
            minLength:0,
            source: [
                <?php foreach ($brand as $v){?>
                "<?=$v['name']?>",
                <?php } ?>
            ]
        });
        $("input[name='brand_name']").focus(function(){
            if($(this).val() == ""){
                $("input[name='brand_name']").autocomplete("search", "");
            }
        });
    });
</script>
<?php \frontend\components\JsBlock::end()?>
