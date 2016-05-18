<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
$this->title = '商品单位管理';
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
$unit_info = \frontend\components\Search::SearchUnit();
?>
<!--内容-->
<div class="container">
	<div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'unit/create')==true){;?>
		<a href="<?=Url::to(['unit/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建单位</span></a>
        <?}?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'unit/update')==true){?>
		<span onclick="javascript:$('#sort').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
	</div>
	<?=Html::beginForm(Url::to(['unit/index']),'post',['id'=>'sort'])?>
	<table class="orders-info">
		<tr>
		    <th>顺序</th>
		    <th>单位名称</th>
		    <th>备注说明</th>
		    <th>操作</th>
		</tr>
        <?php foreach($countries as $item):?>

		<tr>
		    <td align="left" width="5%"><input type="text" name="sort[<?=$item['unit_id']?>]" value="<?echo $item['sort']?>"></td>
		   	<td width="15%"><? echo $item['unit'] ?></td>
		   	<td><? echo $item['remark'] ?></td>
		   	<td align="right" width="10%">
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'unit/delete')==true){;?>
                <?=Html::a('<i class="iconfont">&#xe605;</i>',['unit/delete','id'=>$item['unit_id']],
                    ['class'=>'orders-infosc',
                        'data'=>[ 'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                            'method' => 'post',],
                    ])?>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'unit/update')==true){?>
		   		<a href="<?=Url::to(['unit/update','id'=>$item['unit_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php } ?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'unit/view')==true){?>
		   		<a href="<?=Url::to(['unit/view','id'=>$item['unit_id']])?>"><i class="iconfont">&#xe60b;</i></a>
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

<!--删除弹窗-->
<!--<div class="orders-sc">-->
<!--	<p class="orders-sct1 clearfix">删除<i class="iconfont">&#xe608;</i></p>-->
<!--	<p class="orders-sct2">您确定要删除这条记录吗？删除后不可恢复！</p>-->
<!--	<div class="orders-sct3">-->
<!--		<span>删除</span>-->
<!--		<span class="orders-sct3qx">取消</span>-->
<!--	</div>-->
<!--</div>-->
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $("input[name='unit']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($unit_info as $v){?>
                    {title:"<?=$v['unit']?>"},
                    <?php }?>
                ]
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>