<?php

/* @var $this yii\web\View */

$this->title = '库存盘点';
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

$this->registerCssFile('@web/statics/css/stocksCheck.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/stocksCheck.js',['depends'=>['yii\web\YiiAsset']]);


$this->registerJs("!function(){
    laydate({elem: '#start-date'});//绑定元素
    laydate({elem: '#end-date'});
}();", \yii\web\View::POS_END);
$good_list = \frontend\components\Search::SearchGoods();
?>
    <!--内容-->
    <div class="container">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <table class="orders-info">
            <tr>
                <th>仓库</th>
                <th>开单人</th>
                <th>开单时间</th>
                <th>盘点完成</th>
                <th>确认人</th>
                <th>确认时间</th>
                <th>操作</th>
            </tr>
            <?php foreach($dataProvider as $item){ ?>
                <tr>
                    <td class="table-left">&nbsp;<?=$item['warehouse_name'].'&nbsp;（'.$item['check_no'].'）';?></td>
                    <td width="10%"><?=$item['add_user_name'];?></td>
                    <td width="10%"><?=date('Y-m-d H:i:s', $item['create_time'])?></td>
                    <td width="6%"><?=($item['status']==1)?'是':'否';?></td>
                    <td width="10%"><?=$item['confirm_user_name'];?></td>
                    <td width="10%"><?=($item['confirm_time']>0)?date('Y-m-d H:i:s', $item['confirm_time']):'';?></td>
                    <td width="8%">
                        <?php if($item['status']==0){?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'check/handle')){?>
                            <a class="sure_icon" href="javascript:;" nctype="<?=$item['check_id']?>"><i class="iconfont icon_1">&#xe612;</i></a>
                        <?php }?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'check/delete')){?>
                            <a class="orders-infosc" href="javascript:;" nctype="<?=$item['check_id']?>"><i class="iconfont">&#xe605;</i></a>
                        <?php }?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'check/update')){?>
                            <a href="<?=Url::to(['check/update','id' => $item['check_id']])?>"><i class="iconfont">&#xe603;</i></a>
                        <?php }?>
                        <?php }?>
                        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'check/view')){?>
                            <a href="<?=Url::to(['check/view','id' => $item['check_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                        <?php }?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php echo \frontend\components\PageWidget::widget([
            'pagination' => $pages,
        ]);?>
    </div>
    <!--弹窗-->
    <!--确认和删除弹窗-->
    <div class="window_1">
        <p>确认该盘点单？确认后将无法修改！</p>
        <a href=""><button class="button button_1">确认</button></a>
        <button class="button" id="button_c">取消</button>
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
                width:400,
                data:[
                    <?php foreach($good_list as $v){?>
                    {title:"<?=$v['name']?>",result:{goods_id:"<?=$v['goods_id']?>"}},
                    <?php }?>
                ]
            });
            //删除
            var sc;
            //点击确认、取消(按钮)关闭弹窗
            $('.sure_icon').click(function(){
                var id = $(this).attr('nctype');
                $('.window_1>a').attr('href','<?=Url::to(['check/handle'])?>&id='+id);
            });

            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['check/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>