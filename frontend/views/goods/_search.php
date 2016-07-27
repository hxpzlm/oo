<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$cat_id = Yii::$app->request->get('cat_id') ? (int)Yii::$app->request->get('cat_id') : 0;
//var_dump($cat_id);exit;
$category= \frontend\models\Goods::GetCategory(0,$cat_id);
?>
<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>
    <div class="seeks clearfix">
        <div class="close_btn"><input type="text" name="name" placeholder="请输入商品中英文名称" value="<?=Yii::$app->request->get('name')?>"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
        <input type="hidden" name="store_id" value="<?=Yii::$app->user->identity->store_id?>">
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
            <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'goods/create')){?>
                <a href="<?=Url::to(['goods/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建商品</span></a>
            <?php }?>
            <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'goods/update')){?>
                <span onclick="javascript:$('#sort').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
            <?php }?>

        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'goods/index')){?><span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span><?php } ?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>条形码</p>
            <input type="text" placeholder="请输入条形码" name="barode_code"  value="<?=Yii::$app->request->get('barode_code')?>"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>品牌名称</p>
            <input type="text" placeholder="请输入商品品牌名" name="brand_name" value="<?=Yii::$app->request->get('brand_name')?>"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>负责人</p>
            <input type="text" placeholder="请输入负责人" name="principal_name"  value="<?=Yii::$app->request->get('supplier_name')?>"/>
        </div>
        <?php
        if(Yii::$app->user->identity->username=='admin'){
            //获取入驻商家
            $store_list = \frontend\components\Search::SearchStore();
            ?>
            <div class="seeks-boxs clearfix">
                <p>入驻商家</p>
                <select name="store_id">
                    <option <?php if(''==Yii::$app->request->get('store_id')){echo 'selected';};?> value=''>请选择</option>
                    <?php foreach($store_list as $value){?>
                        <option <?php if($value['store_id']==Yii::$app->request->get('store_id')){echo 'selected';};?> value="<?=$value['store_id']?>"><?=$value['name']?></option>
                    <?php }?>
                </select>
            </div>
        <?php }?>
    </div>
<?php ActiveForm::end(); ?>
<div class="seeks-boxs seeks-boxst1 clearfix">
    <p>分类选择</p>
    <select name="cat_id" id='catname'>
        <option value="">请选择</option>
        <?php echo $category; ?>
    </select>
</div>