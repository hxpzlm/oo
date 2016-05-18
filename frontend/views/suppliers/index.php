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
		<span onclick="javascript:$('#sort_l').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>

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
            <td><input type="text" name="sort[<?=$item['suppliers_id']?>]" value="<?=$item['sort']?>"></td>
		    <td><?= $item['name']; ?> </td>
		   	<td><?= $item['country']; ?></td>
		   	<td><?= $item['city']; ?></td>
		   	<td><?= $item['mobile']; ?></td>
		   	<td><?= $item['contact_man']; ?></td>
		   	<td><?= $item['fax']; ?></td>
		   	<td><?= $item['email']; ?></td>
		   	<td><?= $item['address']; ?></td>
            <td><?= $item['status']==1?'正常':'停用'; ?></td>
		   	<td><?= $item['shop_manage_principal']; ?></td>
		   	<td>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'suppliers/delete')){?>
                <?= Html::a('<i class="iconfont">&#xe605;</i>', ['delete', 'id' => $item['suppliers_id']], [
                    'class' => 'orders-infosc',
                    'data' => [
                        'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                        'method' => 'post',
                    ],
                ]) ?>
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

<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            //订单编号搜索
            $("input[name='SuppliersSearch[name]']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($suppliers as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>