<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
$this->title = '品牌列表';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$brand_info = \frontend\components\Search::SearchBrand();
?>
<!--内容-->
<div class="container">
	<div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'brand/create')==true){;?>
		<a href="<?=Url::to(['brand/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建品牌</span></a>
        <?}?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'brand/update')==true){?>
		<span onclick="javascript:$('#sort').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
	</div>
	<?=Html::beginForm(Url::to(['brand/index']),'post',['id'=>'sort'])?>
	<table class="orders-info">
		<tr>
		    <th>顺序</th>
		    <th>品牌名称</th>
		    <th>状态</th>
		    <th>备注说明</th>
		    <th>操作</th>
		</tr>
        <?php foreach($countries as $item):?>

		<tr>
		    <td class="table-left" width="5%">&nbsp;<input type="text" name="sort[<?=$item['brand_id']?>]" value="<?echo $item['sort']?>"></td>
		   	<td width="12%" class="table-left"><? echo $item['name'] ?></td>
		   	<td width="5%"><?php if($item['status']==1){echo '正常';}else{echo '停用';} ?></td>
		   	<td class="table-left"><? echo $item['remark'] ?></td>
		   	<td width="5%">
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'brand/delete')==true){;?>
                    <a class="orders-infosc" href="javascript:;" nctype="<?=$item['brand_id']?>"><i class="iconfont">&#xe605;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'brand/update')==true){?>
		   		<a href="<?=Url::to(['brand/update','id'=>$item['brand_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php } ?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'brand/view')==true){?>
		   		<a href="<?=Url::to(['brand/view','id'=>$item['brand_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                <?php }?>
		   	</td>
		</tr>
        <?php endforeach;?>
	</table>
    <?=Html::endForm();?>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
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
            $("input[name='name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($brand_info as $v){?>
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
            //删除
            var sc;
            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['brand/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>