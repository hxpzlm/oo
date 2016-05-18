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
                <td><input type="text" name="sort[<?=$item['goods_id']?>]" value="<?=$item['sort']?>"></td>
                <td><?php echo  $item['name'].'  ('.$item['spec'].')'; ?> </td>
                <td><?php echo  $item['brand_name']; ?></td>
                <td><?php echo  $item['unit_name']; ?></td>
                <td><?php echo  $item['barode_code']; ?></td>
                <td><?php echo  $item['weight']?$item['weight'].'kg':''; ?></td>
                <td><?php echo  $item['volume']?$item['volume'].'m<sup>3</sup>':''; ?></td>
                <td><?php echo  $item['shelf_life']?$item['shelf_life'].'天':''; ?></td>
                <td><?php echo \frontend\models\Goods::GetCategory_name($item['cat_id']).$item['cat_name'] ?></td>
                <td><?php echo  $item['principal_name']; ?></td>
                <td>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'goods/delete')){?>
                        <!--<a class="orders-infosc" href="/index.php?r=suppliers%2Fdelete&id=1"><i class="iconfont">&#xe605;</i></a>-->
                        <?= Html::a('<i class="iconfont">&#xe605;</i>', ['delete', 'id' => $item['goods_id']], [
                            'class' => 'orders-infosc',
                            'data' => [
                                'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                                'method' => 'post',
                            ],
                        ]) ?>
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
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $("input[name='name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($goods_info as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
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
                    {title:"<?php echo $v['username']?>"},
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

        });

    </script>
<?php \frontend\components\JsBlock::end()?>