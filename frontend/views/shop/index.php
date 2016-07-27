<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);

$this->title = '销售平台列表';
$this->params['breadcrumbs'][] = $this->title;
$shop = \frontend\components\Search::SearchShop();
?>
<!--内容-->
<div class="container">
    <div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'shop/create')){?>
        <a href="<?=Url::to(['shop/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建销售平台</span></a>
        <?php }?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'shop/update')){?>
        <span onclick="javascript:$('#sort_l').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
    </div>
    <?=Html::beginForm(Url::to(['shop/index']),'post',['id'=>'sort_l']);?>
    <table class="orders-info">
        <tr>
            <th>顺序</th>
            <th>平台名称</th>
            <th>状态</th>
            <th>备注说明</th>
            <th>操作</th>
        </tr>
        <?php foreach($dataProvider as $item){ ?>
            <tr>
                <td width="8%" class="table-left">&nbsp;<input type="text" name="sort[<?=$item['shop_id']?>]" value="<?=$item['sort']?>"></td>
                <td width="10%"><?= $item['name']; ?> </td>
                <td width="7%"><?= $item['status']==1 ? '正常' : '停用'?></td>
                <td class="table-left"><?= $item['remark']; ?></td>
                <td width="5%">
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'shop/delete')){?>
                        <a class="orders-infosc" href="javascript:;" nctype="<?=$item['shop_id']?>"><i class="iconfont">&#xe605;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'shop/update')){?>
                        <a href="<?=Url::to(['shop/update','id' => $item['shop_id']])?>"><i class="iconfont">&#xe603;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'shop/view')){?>
                        <a href="<?=Url::to(['shop/view','id' => $item['shop_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                    <?php }?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?=Html::endForm(); ?>

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
            $("input[name='ShopSearch[name]']").focus(function(){
                var html = '<table><tbody>';
                <?php foreach($shop as $v){?>
                html+='<tr class=""><td><div><?=$v['name']?></div></td></tr>';
                <?php }?>
                html+="</tbody></table>";
                $('.bigautocomplete-layout').html(html);
                $('.bigautocomplete-layout').css({'display':'block','width': '510px','top': '227px','left': '9.5px'});
            });
            $("input[name='ShopSearch[name]']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($shop as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ],
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='ShopSearch[name]']").val('');
                        $(this).hide();
                    })
                }
            });
            //删除
            var sc;
            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['shop/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>