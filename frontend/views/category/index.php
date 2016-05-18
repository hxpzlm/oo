<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;

$parent_id =Yii::$app->request->get('parent_id')?Yii::$app->request->get('parent_id'):'0';
$this->title = '商品分类列表';
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
$category_info = \frontend\components\Search::SearchCategory();
?>
<!--内容-->
<div class="container">
	<div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if($parent_id>0){?>
        <a href="<?=Url::to(['category/index'])?>"><span class="seeks-x2"><img src="statics/img/u2492.png" style="width: 20px;height: 10px;margin: 0 10px;"/>返回</span></a>
        <?php }?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'category/create')==true){;?>
		<a href="<?=Url::to(['category/create','parent_id'=>$parent_id])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建分类</span></a>
        <?}?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'category/update')==true){?>
		<span onclick="javascript:$('#sort').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
	</div>
	<?=Html::beginForm(Url::to(['category/index']),'post',['id'=>'sort'])?>
	<table class="orders-info">
		<tr>
		    <th>顺序</th>
		    <th>分类名称</th>
		    <th>状态</th>
		    <th>备注说明</th>
		    <th>操作</th>
		</tr>
        <?php foreach($countries as $item):?>

		<tr>
		    <td align="left" width="5%"><input type="text" name="sort[<?=$item['cat_id']?>]" value="<?=$item['sort']?>"></td>
		   	<td width="15%"><?= empty($parent_id)?Html::a($item['name'], ['category/index','parent_id'=>$item['cat_id']],['style'=>'color:blue']):Html::a($item['name'], ['category/index','parent_id'=>$item['cat_id']],['style'=>'color:#333'])?></td>
		   	<td width="5%"><?=($item['status']==1)?"正常":"停用";?></td>
            <td><?=$item['remark'];?></td>
		   	<td align="right" width="10%">
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'category/delete')==true){;?>
                <?=Html::a('<i class="iconfont">&#xe605;</i>',['category/delete','id'=>$item['cat_id']],
                    ['class'=>'orders-infosc',
                        'data'=>[ 'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                            'method' => 'post',],
                    ])?>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'category/update')==true){?>
		   		<a href="<?=Url::to(['category/update','id'=>$item['cat_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php } ?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'category/view')==true){?>
		   		<a href="<?=Url::to(['category/view','id'=>$item['cat_id']])?>"><i class="iconfont">&#xe60b;</i></a>
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
            $("input[name='name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($category_info as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>