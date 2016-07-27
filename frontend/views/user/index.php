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
        <div class="close_btn"><input type="text" placeholder="请输入用户名称" name="username"><img src="statics/img/close_icon.jpg" class="img_css"></div>

		<button type="submit"><i class="iconfont">&#xe60d;</i>搜索</button>
		<?php ActiveForm::end(); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/add')){?>
		<a href="<?=Url::to(['user/add'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建用户</span></a>
        <?php }?>
		<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/update')){?>
		<span onclick="javascript:$('#sort_l').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
		<?php }?>
		
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
		    <td width="8%">&nbsp;<input type="text" name="sort[<?=$item['user_id']?>]" value="<?=$item['sort']?>"></td>
		    <td><?=$item['username']?></td>
		   	<td width="5%"><?=($item['status']==1)?"正常":"停用"?></td>
		   	<td width="12%"><?=empty($item['store_name'])?"系统管理员":$item['store_name'];?></td>
		   	<td width="12%"><?=$item['real_name']?></td>
		   	<td width="5%"><?=($item['sex']==1)?"男":"女";?></td>
		   	<td width="10%"><?=$item['mobile']?></td>
		   	<td width="15%"><?=$item['email']?></td>
		   	<td width="10%"><?=date('Y-m-d H:i:s',$item['last_login_time'])?></td>
		   	<td width="7%">
				<?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/profile') && Yii::$app->user->identity->type==1){?>
				<a class="password" href="javascript:;" nctype="<?=$item['user_id']?>"><i class="iconfont">&#xe621;</i></a>
				<?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/delete')){?>
				<a class="orders-infosc" href="javascript:;" nctype="<?=$item['user_id']?>"><i class="iconfont">&#xe605;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'user/update') && $item['username']!=Yii::$app->user->identity->username){?>
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


<div class="mima">
	<p class="orders-sct1 clearfix">密码初始化<span class="iconfont mima_i">&#xe608;</span></p>
	<p class="orders-sct2">您确定要将该用户的密码初始化为vtg123吗？</p>
	<div class="orders-sct3">
		<a href="" data-method="post"><lable class="orders-sct3qx mima_span">确定</lable></a>
		<lable class="mima_span mima_cancle">取消</lable>
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
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='username']").val('');
                        $(this).hide();
                    })
                }
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
	$('.password').click(function () {
		var id = $(this).attr('nctype');
		$('.orders-sct3>a').attr('href','<?=Url::to(['user/profile','action'=>'init'])?>&user_id='+id);
		showWindow('.mima');
	});

	$('.mima_i,.mima_span,.mima_cancle').on('click',function () {
		closeWindow('.mima');
	});
});


	</script>
<?php \frontend\components\JsBlock::end()?>
<style>
	.mima{
		display: none;
		z-index: 9999;
		position: fixed;
		background: #fff;
		top: 40%;
		left: 45%;
		background: #f2f2f2;
	}

	.mima .orders-sct1{
		background: #fff;
	}

	.mima span{
		float: right;
		font-size: 20px;
		cursor: pointer;
	}

	.mima .orders-sct2{
		padding: 20px 20px;
	}

	.mima_span{
		margin: 0 20px;
		padding: 5px 20px;
		border-radius: 5px;
		cursor: pointer;
	}

	.mima_cancle{
		background: #666;
		color: #fff;
	}
</style>
