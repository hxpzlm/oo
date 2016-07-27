<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\Goods */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品管理列表';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery.cookie.js',['depends'=>['yii\web\YiiAsset']]);
$goods_info = \frontend\components\Search::SearchGoods();
$brnd_info = \frontend\components\Search::SearchBrand();
$principal_info = \frontend\components\Search::SearchUser();
?>
<!--内容-->
<div class="container">
    <?php echo $this->render('_search', ['model' => $model,]); ?>
    <?=Html::beginForm(Url::to(['goods/index']),'post',['id'=>'sort']);?>
    <table class="orders-info">
        <tr>
            <th>顺序</th>
            <th>商品中英文名称（含规格）</th>
            <th>品牌</th>
            <th>单位</th>
            <th>条形码</th>
            <th>净重</th>
            <th>体积</th>
            <th>保质期</th>
            <th>商品所属分类</th>
            <th>负责人</th>
            <th>操作</th>
        </tr>
        <?php foreach($countries as $item){ ?>
            <tr>
                <td width="5%" class="table-left">&nbsp;<input type="text" name="sort[<?=$item['goods_id']?>]" value="<?=$item['sort']?>"></td>
                <td class="table-left"><?php echo  $item['name'].'  ('.$item['spec'].')'; ?> </td>
                <td width="12%"><?php echo  $item['brand_name']; ?></td>
                <td width="3%"><?php echo  $item['unit_name']; ?></td>
                <td width="9%"><?php echo  $item['barode_code']; ?></td>
                <td width="8%"><?php echo  $item['weight']?$item['weight'].'kg':''; ?></td>
                <td wdith="8%"><?php echo  $item['volume']?$item['volume'].'m<sup>3</sup>':''; ?></td>
                <td width="6%"><?php echo  $item['shelf_life']?$item['shelf_life'].'天':''; ?></td>
                <td width="8%"><?php echo \frontend\models\Goods::GetCategory_name($item['cat_id']).$item['cat_name'] ?></td>
                <td width="6%"><?php echo  $item['principal_name']; ?></td>
                <td width="5%">
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'goods/delete')){?>
                        <a class="orders-infosc" href="javascript:;" nctype="<?=$item['goods_id']?>"><i class="iconfont">&#xe605;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'goods/update')){?>
                        <a href="<?=Url::to(['goods/update','id' => $item['goods_id']])?>"><i class="iconfont">&#xe603;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/view')){?>
                        <a href="<?=Url::to(['goods/view','id' => $item['goods_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                    <?php }?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?=Html::endForm(); ?>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
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
            $("input[name='name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($goods_info as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ],
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='name']").val('');
                        $(this).hide();
                    })
                }
            });
            $("input[name='brand_name']").bigAutocomplete({
                width:200,data:[
                    <?php foreach($brnd_info as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
            });
            $("input[name='principal_name']").bigAutocomplete({
                width:200,data:[
                    <?php foreach($principal_info as $v){?>
                    {title:"<?php echo $v['real_name']?>"},
                    <?php }?>
                ]
            });
            $("input[name='barode_code']").bigAutocomplete({
                width:200,data:[
                    <?php foreach($goods_info as $v){?>
                    {title:"<?php echo $v['barode_code']?>"},
                    <?php }?>
                ]
            });
            $('#catname').change(function () {
                var aa=$(this).val();
                location.href='index.php?r=goods&index&cat_id='+aa;
            });
            //删除
            var sc;
            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['goods/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });

    </script>
<?php \frontend\components\JsBlock::end()?>