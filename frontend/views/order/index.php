<?php

/* @var $this yii\web\View */

$this->title = '销售订单列表';
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
$order = \frontend\components\Search::SearchOrder();
?>
<!--内容-->
<div class="container">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <table class="orders-info">
        <tr>
            <th>仓库</th>
            <th>销售平台</th>
            <th>订单编号</th>
            <th>商品中英文名称（含规格）</th>
            <th>商品数量</th>
            <th>实收款</th>
            <th>销售日期</th>
            <th>客户帐号</th>
            <th>出库状态</th>
            <th>操作</th>
        </tr>
        <?php foreach($dataProvider as $item){ ?>
            <tr>
                <td>
                    <?php
                    if($item['warehouse_id']>0){
                        $v = $query->select('name')->from($tablePrefix.'warehouse')->where('warehouse_id='.$item['warehouse_id'])->one();
                        echo $v['name'];
                    }
                    ?>
                </td>
                <td class="table-tdw">
                    <?php
                    if($item['shop_id']>0){
                        $v = $query->select('name')->from($tablePrefix.'shop')->where('shop_id='.$item['shop_id'])->one();
                        echo $v['name'];
                    }
                    ?>
                </td>
                <td><?=$item['order_no'];?></td>
                <td>
                    <?php
                    if(!empty($item['data'])){
                        foreach($item['data'] as $v){
                            echo BaseStringHelper::truncate($v['goods_name'],32).'&nbsp'.$v['spec'].'<br/>';
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if(!empty($item['data'])){
                        foreach($item['data'] as $v){
                            echo $v['number'].'<br/>';
                        }
                    }
                    ?>
                </td>

                <td><?=$item['real_pay'];?> 元</td>
                <td><?= date('Y-m-d',$item['sale_time']); ?></td>
                <td><?=$item['customer_name']?></td>
                <td><?=$item['delivery_status']==1 ? '是':'否'; ?></td>
                <td>
                    <?php if($item['delivery_status']!=1){?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/delete')){?>
                        <?= Html::a('<i class="iconfont">&#xe605;</i>', ['delete', 'id' => $item['order_id']], [
                            'class' => 'orders-infosc',
                            'data' => [
                                'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                                'method' => 'post',
                            ],
                        ]) ?>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/update')){?>
                        <a href="<?=Url::to(['order/update','id' => $item['order_id']])?>"><i class="iconfont">&#xe603;</i></a>
                    <?php }?>
                <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'order/view')){?>
                        <a href="<?=Url::to(['order/view','id' => $item['order_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                    <?php }?>
                </td>
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
            //订单编号搜索
            $("input[name='order_no']").focus(function(){
                var html = '<table><tbody>';
                <?php foreach($order as $v){?>
                html+='<tr class=""><td><div><?=$v['order_no']?></div></td></tr>';
                <?php }?>
                html+="</tbody></table>";
                $('.bigautocomplete-layout').html(html);
                $('.bigautocomplete-layout').css({'display':'block','width': '510px','top': '227px','left': '9.5px'});
            });
            $("input[name='order_no']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($order as $v){?>
                    {title:"<?=$v['order_no']?>"},
                    <?php }?>
                ]
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>