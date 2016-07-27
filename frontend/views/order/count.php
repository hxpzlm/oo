<?php

/* @var $this yii\web\View */

$this->title = '待出货统计';
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\BaseStringHelper;
use frontend\assets\AppAsset;
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

$query = new \yii\db\Query();
$tablePrefix = Yii::$app->getDb()->tablePrefix;
$warehouse = \frontend\components\Search::SearchWarehouse();
$order = \frontend\components\Search::SearchOrder();
?>
<!--内容-->
<div class="container">
    <?php echo $this->render('countSearch', ['model' => $searchModel]); ?>
    <table class="orders-info">
        <tr>
            <th>发货仓库</th>
            <th>收货人</th>
            <th>联系电话</th>
            <th>收货地址</th>
            <th>邮政编码</th>
            <th>证件号码</th>
            <th>商品中英文名称（含规格）</th>
            <th>品牌</th>
            <th>条形码</th>
            <th>商品数量</th>
            <th>实收款</th>
            <th>物流公司</th>
        </tr>
        <?php foreach($dataProvider as $item){ ?>
            <tr>
                <td width="5%" class="table-left">&nbsp;
                    <?php
                    if($item['warehouse_id']>0){
                        $v = $query->select('name')->from($tablePrefix.'warehouse')->where('warehouse_id='.$item['warehouse_id'])->one();
                        echo $v['name'];
                    }
                    ?>
                </td>
                <td width="5%"><?=$item['accept_name'];?></td>
                <td width="6%"><?=$item['accept_mobile'];?></td>
                <td width="18%" class="table-left"><?=$item['accept_address'];?></td>
                <td width="6%"><?=$item['zcode'];?></td>
                <td width="9%"><?=$item['accept_idcard'];?></td>
                <?php
                if($item['order_id']>0){
                    $data = $query->select('goods_id,goods_name,spec,brand_name,number')->from($tablePrefix.'order_goods')->where(['order_id'=>$item['order_id']])->all();
                }
                ?>

                <td class="table-left">
                    <?php
                    if(!empty($data)){
                        foreach($data as $v){
                            echo BaseStringHelper::truncate($v['goods_name'],32).'&nbsp'.$v['spec'].'<br/>';
                        }
                    }
                    ?>
                </td>
                <td width="7%">
                    <?php
                    if(!empty($data)){
                        foreach($data as $v){
                            echo $v['brand_name'].'<br/>';
                        }
                    }
                    ?>
                </td>
                <td width="6%">
                    <?php
                    if(!empty($data)){
                        foreach($data as $v){
                            if($v['goods_id']>0){
                                $v = $query->select('barode_code')->from($tablePrefix.'goods')->where('goods_id='.$v['goods_id'])->one();
                                echo $v['barode_code'].'<br/>';
                            }
                        }
                    }
                    ?>
                </td>
                <td width="3%">
                    <?php
                    if(!empty($data)){
                        foreach($data as $v){
                            echo $v['number'].'<br/>';
                        }
                    }
                    ?>
                </td>

                <td width="5%" class="table-right"><?=$item['real_pay'];?>元</td>
                <td width="10%"><?=$item['delivery_name']?></td>
            </tr>
        <?php } ?>
    </table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pages,
    ]);;?>
</div>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            //仓库搜索
            $("input[name='warehouse_name']").bigAutocomplete({
                width:510,
                data:[
                    <?php foreach($warehouse as $v){?>
                    {title:"<?=$v['name']?>",result:{warehouse_id:"<?=$v['warehouse_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='warehouse_id']").val(data.result.warehouse_id);

                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='warehouse_name']").val('');
                        $(this).hide();
                    })
                }
            });

            //订单编号搜索
            $("input[name='order_no']").bigAutocomplete({
                width:200,data:[
                    <?php foreach($order as $v){?>
                    {title:"<?=$v['order_no']?>"},
                    <?php }?>
                ]
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>