
<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$this->title = '系统设置-入驻商家列表';
$this->params['breadcrumbs'][] = $this->title;
$store_info = \frontend\components\Search::SearchStore();
?>
<!--内容-->
<div class="container">
	<div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'store/create')==true){;?>
		<a href="<?=Url::to(['store/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建入驻商家</span></a>
        <?}?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'store/update')==true){;?>
		<span  onclick="javascript:$('#sort').submit()"><i class="iconfont" >&#xe60e;</i></I>确认排序</span>
        <?php }?>
	</div>
    <?=Html::beginForm(Url::to(['store/index']),'post',['id'=>'sort']);?>
	<table class="orders-info">
		<tr>
		    <th>顺序</th>
		    <th>入驻商家名称</th>
		    <th>状态</th>
		    <th>备注说明</th>
		    <th>操作</th>
		</tr>
        <?php
        foreach ($countries as $item):?>
            <tr>
                <td class="table-left" width="5%">&nbsp;<input type="text" name="sort[<?=$item['store_id']?>]" value="<?=$item['sort']?>"></td>
                <td class="table-left" width="10%"><?= $item['name'] ?> </td>
                <td  width="7%"><?php if($item['status']==0){echo '停用';}else{echo '正常';} ?></td>
                <td class="table-left"><?= $item['remark'] ?></td>
                <td width="5%">
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'store/delete')==true){?>
                    <a class="orders-infosc" href="javascript:;" nctype="<?=$item['store_id']?>"><i class="iconfont">&#xe605;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'store/update')==true){?>
		   		<a href="<?=Url::to(['store/update','id'=>$item['store_id']])?>"><i class="iconfont">&#xe603;</i></a>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'store/view')==true){?>
		   		<a href="<?=Url::to(['store/view','id'=>$item['store_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                <?php }?>
		   	</td>
		</tr>
    <?php endforeach;?>

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
                <?php foreach($store_info as $v){?>
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
            $('.orders-sct3>a').attr('href','<?=Url::to(['store/delete'])?>&id='+id);
            sc = $(".orders-sc").bPopup();
        })
        $(".orders-sct1 i,.orders-sct3 span").click(function(){
            sc.close();
        });
    });
</script>
<?php \frontend\components\JsBlock::end()?>

