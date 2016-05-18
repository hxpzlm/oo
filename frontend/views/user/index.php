<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
$users = \frontend\components\Search::SearchUser();
?>
<!--内容-->
<div class="container">
	<div class="seeks clearfix">
		<?php $form = ActiveForm::begin([
			'action' => ['index'],
			'method' => 'get',
		]); ?>
		<input type="text" placeholder="请输入用户名称" name="username">

		<button type="submit"><i class="iconfont">&#xe60d;</i>搜索</button>
		<?php ActiveForm::end(); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/add')){?>
		<a href="<?=Url::to(['user/add'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建用户</span></a>
        <?php }?>
		<span onclick="javascript:$('#sort_l').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
	</div>
	<?=Html::beginForm('','post',['id'=>'sort_l']);?>
	<table class="orders-info">
		<tr>
		    <th>顺序</th>
		    <th>账号</th>
		    <th>状态</th>
		    <th>所属入驻商家</th>
		    <th>姓名</th>
		    <th>性别</th>
		    <th>手机</th>
		    <th>邮箱</th>
		    <th>最后登陆时间</th>
		    <th>操作</th>
		</tr>
		<?php foreach($dataProvider as $item){?>
		<tr>
		    <td><input type="text" name="sort[<?=$item['user_id']?>]" value="<?=$item['sort']?>"></td>
		    <td><?=$item['username']?></td>
		   	<td><?=($item['status']==1)?"正常":"停用"?></td>
		   	<td><?=empty($item['store_name'])?"系统管理员":$item['store_name'];?></td>
		   	<td><?=$item['real_name']?></td>
		   	<td><?=($item['sex']==1)?"男":"女";?></td>
		   	<td><?=$item['mobile']?></td>
		   	<td><?=$item['email']?></td>
		   	<td><?=date('Y-m-d H:i:s',$item['last_login_time'])?></td>
		   	<td>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/delete')){?>
				<a class="orders-infosc" href="javascript:;" nctype="<?=$item['user_id']?>"><i class="iconfont">&#xe605;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/update')){?>
		   		<a href="<?=Url::to(['user/update','user_id' => $item['user_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/view')){?>
		   		<a href="<?=Url::to(['user/view','user_id' => $item['user_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                <?php }?>
		   	</td>
		</tr>
       <?php }?>
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
			<a href="" data-method="post"><span class="orders-sct3qx">确定</span></a>
			<span>取消</span>
		</div>
</div>

<?php \frontend\components\JsBlock::begin()?>
	<script>
		$(function(){
			//用户数据过滤
			$("input[name='username']").bigAutocomplete({
				width:510,data:[
					<?php foreach($users as $v){?>
					{title:"<?=$v['username']?>"},
					<?php }?>
				],
			});
			
	var sc;
	$('.orders-infosc').click(function(){
		var id = $(this).attr('nctype');
		$('.orders-sct3>a').attr('href','<?=Url::to(['user/delete'])?>&user_id='+id);
        sc = $(".orders-sc").bPopup();
	})
	$(".orders-sct1 i,.orders-sct3 span").click(function(){
        sc.close();
    });
			
		});
	</script>
<?php \frontend\components\JsBlock::end()?>