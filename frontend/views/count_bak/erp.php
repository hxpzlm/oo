<?php

/* @var $this yii\web\View */

$this->title = '进销存统计';
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
?>
<!--内容-->
<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['erp'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" name="goods_name" placeholder="请直接选择或输入商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/erp')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>品牌</p>
            <input type="text" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>" />
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
        <div class="seeks-boxs seeks-boxst2 clearfix">
            <p>时间</p>
            <input type="text" placeholder="开始时间" name="time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('time_start')?>"/>
            <span>-</span>
            <input type="text" placeholder="终止时间" name="time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('time_end')?Yii::$app->request->get('time_end'):date('Y-m-d', time())?>"/>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <table class="orders-info">
        <tr>
            <th>商品中英文名称</th>
            <th>规格</th>
            <th>品牌</th>
            <th>单位</th>
            <th>采购数量</th>
            <th>采购金额(元)</th>
            <th>销售数量</th>
            <th>销售金额(元)</th>
            <th>库存数量</th>
            <th>库存金额(元)</th>

        </tr>
        <?php foreach ($model as $v){?>
        <tr>
            <td><?=$v['goods_name']?></td>
            <td><?=$v['spec']?></td>
            <td><?=$v['brand_name']?></td>
            <td><?=$v['unit_name']?></td>
            <td><?=$v['p_nums']?></td>
            <td><?=$v['p_price']?></td>
            <td><?=empty($v['s_nums'])?0:$v['s_nums']?></td>
            <td><?=empty($v['totle_price'])?'0.00':$v['totle_price']?></td>
            <td><?=$v['p_nums']-$v['s_nums']?></td>
            <td><?=round(($v['p_nums']-$v['s_nums'])*$v['buy_price'],2)?></td>
        </tr>
        <?php } ?>
    </table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
        $("input[name='brand_name']").bigAutocomplete({
            width:200,data:[
                <?php foreach($brand as $v){?>
                {title:"<?=$v['name']?>"},
                <?php }?>
            ],
        });
    });
</script>
<?php \frontend\components\JsBlock::end()?>
