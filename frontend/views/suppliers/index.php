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

$this->title = '供应商列表';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'store/create');

$suppliers = \frontend\components\Search::SearchSupplier();
?>
<!--内容-->
<div class="container">
	<div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/create')){?>
		<a href="<?=Url::to(['suppliers/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建供应商</span></a>
        <?php }?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/update')){?>
		<span onclick="javascript:$('#sort_l').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
	</div>
    <?=Html::beginForm(Url::to(['suppliers/index']),'post',['id'=>'sort_l']);?>
	<table class="orders-info">
		<tr>
		    <th>顺序</th>
		    <th>供应商名称</th>
		    <th>国别</th>
		    <th>城市名</th>
		    <th>联系人</th>
		    <th>联系电话</th>
		    <th>传真</th>
		    <th>邮箱</th>
		    <th>联系地址</th>
            <th>状态</th>
		    <th>负责人</th>
		    <th>操作</th>
		</tr>
        <?php foreach($dataProvider as $item){ ?>
		<tr>
            <td width="5%" class="table-left">&nbsp;&nbsp;<input type="text" style="text-align: center" name="sort[<?=$item['suppliers_id']?>]" value="<?=$item['sort']?>"></td>
		    <td class="table-left"><?= $item['name']; ?> </td>
		   	<td width="5%"><?= $item['country']; ?></td>
		   	<td width="8%"><?= $item['city']; ?></td>
		   	<td width="7%" class="table-left"><?= $item['contact_man']; ?></td>
            <td width="7%"><?= $item['mobile']; ?></td>
		   	<td width="7%"><?= $item['fax']; ?></td>
		   	<td width="12%" class="table-left"><?= $item['email']; ?></td>
		   	<td width="18%" class="table-left"><?= $item['address']; ?></td>
            <td width="3%"><?= $item['status']==1?'正常':'停用'; ?></td>
		   	<td width="5%"><?= $item['shop_manage_principal']; ?></td>
		   	<td width="5%">
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/delete')){?>
                    <a class="orders-infosc" href="javascript:;" nctype="<?=$item['suppliers_id']?>"><i class="iconfont">&#xe605;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/update')){?>
		   		<a href="<?=Url::to(['suppliers/update','id' => $item['suppliers_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/view')){?>
		   		<a href="<?=Url::to(['suppliers/view','id' => $item['suppliers_id']])?>"><i class="iconfont">&#xe60b;</i></a>
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
            $("input[name='SuppliersSearch[name]']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($suppliers as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ],
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='SuppliersSearch[name]']").val('');
                        $(this).hide();
                    })
                }
            });
        });
        //删除
        var sc;
        $('.orders-infosc').click(function(){
            var id = $(this).attr('nctype');
            $('.orders-sct3>a').attr('href','<?=Url::to(['suppliers/delete'])?>&id='+id);
            sc = $(".orders-sc").bPopup();
        })
        $(".orders-sct1 i,.orders-sct3 span").click(function(){
            sc.close();
        });
    </script>
<?php \frontend\components\JsBlock::end()?>