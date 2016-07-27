<?php

/* @var $this yii\web\View */

$this->title = '库存调剂列表';
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\BaseStringHelper;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
    laydate({elem: '#start-date'});//绑定元素
    laydate({elem: '#end-date'});
}();", \yii\web\View::POS_END);
$good_list = \frontend\components\Search::SearchGoods();
$brand_list = \frontend\components\Search::SearchBrand();
?>
    <!--内容-->
    <div class="container">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <table class="orders-info">
            <tr>
                <th>仓库</th>
                <th>商品中英文名称（含规格）</th>
                <th>品牌</th>
                <th>条形码</th>
                <th>调剂数量</th>
                <th>调剂日期</th>
                <th>入库状态</th>
                <th>入库日期</th>
                <th>操作</th>
            </tr>
            <?php foreach($dataProvider as $item){ ?>
                <tr>
                    <td class="table-left" width="12%">&nbsp;<?=$item['from_warehouse_name'].' -> '.$item['to_warehouse_name']?></td>
                    <td class="table-left"><?=BaseStringHelper::truncate($item['goods_name'],32).' '.$item['spec'];?> </td>
                    <td width="6%"><?=$item['brand_name'];?></td>
                    <td width="6%"><?=$item['barode_code']?></td>
                    <td width="5%"><?=$item['number'];?></td>
                    <td width="7%"><?=$item['update_time']>0?date('Y-m-d',$item['update_time']):'';?></td>
                    <td width="5%"><?=$item['status']==1?'入库':'未入库';?></td>
                    <td width="10%"><?=$item['confirm_time']>0?date('Y-m-d',$item['confirm_time']):'';?></td>
                    <td width="7%">
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/handle')){?>
                        <?php if($item['status']==0){?>
                                <?= Html::a('<i class="iconfont sellDe-deli icon-queren"></i>', ['handle', 'id' => $item['moving_id'],'action'=>'comfirm'], [
                                    'class' => 'orders-infosc',
                                    'data' => [
                                        'confirm' => '确认该库存调剂的入库操作？',
                                        'cancle'    =>'确认取消该库存调剂的入库操作？',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                        <?php }else{?>
                                <?= Html::a('<i class="iconfont sellDe-deli icon-quxiao"></i>', ['handle', 'id' => $item['moving_id'],'action'=>'cancle'], [
                                    'class' => 'orders-infosc',
                                    'data' => [
                                        'confirm' => '确认取消该库存调剂的入库操作？',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                        <?php }?>
                        <?php }?>
                        <?php if($item['status']==0){?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/delete')){?>
                                <a class="orders-infosc" href="javascript:;" nctype="<?=$item['moving_id']?>"><i class="iconfont">&#xe605;</i></a>
                        <?php }?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/update')){?>
                            <a href="<?=Url::to(['moving/update','id' => $item['moving_id']])?>"><i class="iconfont">&#xe603;</i></a>
                        <?php }?>
                        <?php }?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'moving/view')){?>
                            <a href="<?=Url::to(['moving/view','id' => $item['moving_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                        <?php }?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <?php echo \frontend\components\PageWidget::widget([
            'pagination' => $pages,
        ]);?>
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

            $("input[name='goods_name']").bigAutocomplete({
                width:510,
                data:[
                    <?php foreach($good_list as $v){?>
                    {title:"<?=$v['name']?>",result:{goods_id:"<?=$v['goods_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='goods_id']").val(data.result.goods_id);
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='goods_name']").val('');
                        $(this).hide();
                    })
                }
            });

            $("input[name='brand_name']").bigAutocomplete({
                width:200,
                data:[
                    <?php foreach($brand_list as $v){?>
                    {title:"<?=$v['name']?>",result:{brand_id:"<?=$v['brand_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='brand_id']").val(data.result.brand_id);
                }
            });
            //删除
            var sc;
            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['moving/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>