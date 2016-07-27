<?php

/* @var $this yii\web\View */

$this->title = '退货订单列表';
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
$goods_list = \frontend\components\Search::SearchGoods();

?>
<!--内容-->
<div class="container">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <table class="orders-info">
        <tr>
            <th>销售平台</th>
            <th>订单编号</th>
            <th>退货商品中英文名称（含规格）</th>
            <th>退货数量</th>
            <th>退货金额</th>
            <th>退货日期</th>
            <th>入库仓库</th>
            <th>入库状态</th>
            <th>入库日期</th>
            <th>操作</th>
        </tr>
        <?php foreach($dataProvider as $item){ ?>
            <tr>
                <td width="7%" class="table-left">&nbsp;&nbsp;
                    <?php
                    if($item['shop_id']>0){
                        $v = $query->select('name')->from($tablePrefix.'shop')->where('shop_id='.$item['shop_id'])->one();
                        echo $v['name'];
                    }
                    ?>
                </td>
                <td width="12%"><?=$item['order_no'];?></td>
                <?php
                if($item['refuse_id']>0){
                    $data = $query->select('goods_name,spec,number')->from($tablePrefix.'refuse_order_goods')->where(['refuse_id'=>$item['refuse_id']])->all();
                }
                ?>
                <td class="table-left">
                    <?php
                    if($item['refuse_id']>0){
                        foreach($data as $v){
                            echo BaseStringHelper::truncate($v['goods_name'],32).'&nbsp'.$v['spec'].'<br/>';
                        }
                    }
                    ?>
                </td>
                <td width="5%">
                    <?php
                    if($item['refuse_id']>0){
                        foreach($data as $v){
                            echo $v['number'].'<br/>';
                        }
                    }
                    ?>
                </td>
                <td width="7%" class="table-right"><?=$item['refuse_amount'];?> 元</td>
                <td width="10%"><?= $item['refuse_time']>0?date('Y-m-d',$item['refuse_time']):'';?></td>
                <td width="10%">
                    <?php
                    if($item['warehouse_id']>0){
                        $v = $query->select('name')->from($tablePrefix.'warehouse')->where('warehouse_id='.$item['warehouse_id'])->one();
                        echo $v['name'];
                    }
                    ?>
                </td>
                <td width="5%"><?=$item['status']==1 ? '是':'否'; ?></td>
                <td width="10%"><?= $item['confirm_time']>0?date('Y-m-d',$item['confirm_time']):''; ?></td>
                <td width="5%">
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'refuse-order/delete')){?>
                        <a class="orders-infosc" href="javascript:;" nctype="<?=$item['refuse_id']?>"><i class="iconfont">&#xe605;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'refuse-order/update')){?>
                        <a href="<?=Url::to(['refuse-order/update','id' => $item['refuse_id']])?>"><i class="iconfont">&#xe603;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'refuse-order/view')){?>
                        <a href="<?=Url::to(['refuse-order/view','id' => $item['refuse_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                    <?php }?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pages,
    ]);;?>
</div>
    <div class="orders-sc">
        <p class="orders-sct1 clearfix">删除<i class="iconfont">&#xe608;</i></p>
        <p class="orders-sct2">您确定要删除这条记录吗？删除后不可恢复！</p>
        <div class="orders-sct3">
            <a href="" data-method="post"><span class="orders-sct3qx" style="cursor: pointer">确定</span></a>
            <span style="cursor: pointer">取消</span>
        </div>
    </div>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            //订单编号搜索
            $("input[name='order_no']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($order as $v){?>
                    {title:"<?=$v['order_no']?>"},
                    <?php }?>
                ],
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='order_no']").val('');
                        $(this).hide();
                    })
                }
            });

            //商品名称
            $("input[name='goods_name']").bigAutocomplete({
                width:400,data:[
                    <?php foreach($goods_list as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
            });
            //删除
            var sc;
            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['refuse-order/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>