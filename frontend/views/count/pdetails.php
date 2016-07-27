<?php

/* @var $this yii\web\View */

$this->title = '进销存明细统计';
use yii\helpers\Html;

use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
AppAsset::register($this);


$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/statistics.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/det_statistics.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerCss("
    /*.seeks-box{display: block;}*/
    .ui-datepicker-calendar {display: none;}
    .ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year{
        width: 45%;
    }

    .ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next{
        display: none;
    }
    
    a.this_bcss,a.this_bcss:hover{color:#333333;text-align:left;display:block;width:100%;}
");
$wh['status']=1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id']=Yii::$app->user->identity->store_id;
}else{
    if(!empty(Yii::$app->request->get('store_id')))
        $wh['store_id']=Yii::$app->request->get('store_id');
}
$ware = \frontend\models\WarehouseModel::findAll($wh);
$brand = \frontend\models\Brand::findAll($wh);
$saleshop =  \frontend\models\Shop::findAll($wh);
?>
<!--主体内容s-->
<div class="stati_mian">
    <?php $form = ActiveForm::begin([
        'action' => ['pdetails'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" id="goods_name" name="goods_name" placeholder="请直接选择或输入商品中英文名称" value="<?=Yii::$app->request->get('goods_name')?>" autocomplete="off"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/pdetails')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>商品品牌</p>
            <input type="text" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>"  autocomplete="off"/>
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
            <input type="text" class="year_rl" name="time"  value="<?=Yii::$app->request->get('time')?>"/>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <!--中间内容部分-->
    <div class="cengar_cnt cen_mian">
        <!--左侧表格部分-->
        <table class="table_left" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th>品牌</th>
                <th>商品中英文名称</th>
                <th>规格</th>
                <th>单位</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val){?>
            <tr>
                <td><a href="#" class="this_bcss" title="<?=$val['brand_name']?>"><?=\yii\helpers\BaseStringHelper::truncate($val['brand_name'],10)?></a></td>
                <td><a href="#" class="this_bcss"  title="<?=$val['name']?>"><?=\yii\helpers\BaseStringHelper::truncate($val['name'],32)?></a></td>
                <td><?=$val['spec']?></td>
                <td><?=$val['unit_name']?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <!--右侧表格部分-->
        <div class="f_fd">
            <table class="table_right" border="0" cellspacing="0" cellpadding="0">
                <thead>
                <tr class="tt_width">
                    <th colspan="<?=count($ware)+3?>" class="title_names" name="k_class">期初结存</th>
                    <th colspan="<?=count($ware)+3?>" class="title_names" name="c_class">采购入库</th>
                    <th colspan="<?=count($saleshop)+3?>" class="title_names" name="x_class">销售出库</th>
                    <th colspan="<?=count($ware)+3?>" class="title_names" name="m_class">期末结存</th>
                </tr>
                <tr class="jh_width">
                    <?php foreach ($ware as $v) {?>
                    <th class="k_class"><?=$v['name']?></th>
                    <?php }?>
                    <th class="k_class">数量小计</th>
                    <th class="k_class">采购单价</th>
                    <th class="k_class">库存总额</th>

                    <?php foreach ($ware as $v) {?>
                        <th class="c_class"><?=$v['name']?></th>
                    <?php }?>
                    <th class="c_class">数量小计</th>
                    <th class="c_class">采购单价</th>
                    <th class="c_class">采购总额</th>

                    <?php foreach ($saleshop as $v) {?>
                        <th class="x_class"><?=$v['name']?></th>
                    <?php }?>
                    <th class="x_class">数量小计</th>
                    <th class="x_class">销售单价</th>
                    <th class="x_class">销售总额</th>

                    <?php foreach ($ware as $v) {?>
                        <th class="m_class"><?=$v['name']?></th>
                    <?php }?>
                    <th class="m_class">数量小计</th>
                    <th class="m_class">采购单价</th>
                    <th class="m_class">库存总额</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $val){?>
                <tr>
                    <?php $ssnums=0;foreach ($ware as $v){
                        $num=0;
                        if($val['stock']){
                            foreach ($val['stock'] as $value){
                                if(@$value['ware_id']==$v['warehouse_id']){
                                    $num = $value['stock_num'];
                                    $ssnums=$ssnums+$num;
                                }

                            }
                        }
                        echo "<td>".$num."</td>";
                    }?>
                    <td><?=$ssnums?></td>
                    <td><?=round($val['avg'],2)?></td>
                    <td><?=round($val['avg']*$ssnums,2)?></td>
                    <?php $pnums=$pamount=0; foreach ($ware as $v){
                        $num=0;
                        if($val['purchase']){
                            foreach ($val['purchase'] as $value){
                                if(@$value['ware_id']==$v['warehouse_id']){
                                    $num = $value['number'];
                                    $pnums=$pnums+$num;
                                    $pamount = $pamount+$value['avg_price'];
                                }

                            }
                        }
                        echo "<td>".$num."</td>";
                    }?>
                    <td><?=$pnums?></td>
                    <td><?=$avg=round($pamount/count($val['purchase']),2)?></td>
                    <td><?=$avg*$pnums?></td>
                    <?php $nums=0;$samount=0;foreach ($saleshop as $v){
                        $num=0;
                        if($val['sales']){
                            foreach ($val['sales'] as $value){
                                if(@$value['shop_id']==$v['shop_id']){
                                    $num = $value['nums'];
                                    $nums=$nums+$num;
                                    $samount = $samount+$value['amount'];
                                }

                            }
                        }
                        echo "<td>".$num."</td>";
                    }?>
                    <td><?=$nums?></td>
                    <td><?=($nums>0)?round($samount/$nums,2):"0.00"?></td>
                    <td><?=$samount?></td>
                    <?php $snums=0;foreach ($ware as $v){
                        $num=0;
                        if($val['stocks']){
                            foreach ($val['stocks'] as $value){
                                if(@$value['ware_id']==$v['warehouse_id']){
                                    $num = $value['stock_num'];
                                    $snums=$snums+$num;
                                }

                            }
                        }
                        echo "<td>".$num."</td>";
                    }?>
                    <td><?=$snums?></td>
                    <td><?=round($val['savg'],2)?></td>
                    <td><?=round($val['savg']*$snums,2)?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <div class="clear"></div>
    <p class="p_centent">注：1、期初：统计月份1日零时，默认上个月份1日零时；2、期末：统计月份最后日24时，默认上个月份最后日24时；3、采购单价、销售单价见其他统计注释。
    </p>
    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
<!--主体内容e-->
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





