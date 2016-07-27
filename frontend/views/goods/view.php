<?php
use frontend\assets\AppAsset;
use yii\helpers\Url;
$this->title = '商品管理-查看';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-look.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>


<!--内容-->
<div class="container">
    <h4 class="orders-newtade">商品基本信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">商品中英文名称:</p>
        <p class="orders-lookt2"><?php echo $model->name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">规格:</p>
        <p class="orders-lookt2"><?php echo $model->spec?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">品牌:</p>
        <p class="orders-lookt2"><?php echo $model->brand_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">单位:</p>
        <p class="orders-lookt2"><?php echo $model->unit_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">条形码:</p>
        <p class="orders-lookt2"><?php echo $model->barode_code?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">净重:</p>
        <p class="orders-lookt2"><?php echo $model->weight?$model->weight.'kg':''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">体积:</p>
        <p class="orders-lookt2"><?php echo $model->volume?$model->volume.'<label>m<sup>3</sup></label>':''?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">保质期:</p>
        <p class="orders-lookt2"><?php echo $model->shelf_life?$model->shelf_life.'天':''?></p>
    </div> <div class="orders-look clearfix">
        <p class="orders-lookt3">主要成分:</p>
        <p class="orders-lookt4"><?php echo $model->element?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">功效:</p>
        <p class="orders-lookt4"><?php echo $model->virtue?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">适用人群:</p>
        <p class="orders-lookt4"><?php echo $model->painter?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">服用方法:</p>
        <p class="orders-lookt4"><?php echo $model->suggest?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">储存方法:</p>
        <p class="orders-lookt4"><?php echo $model->store_mode?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt3">介绍:</p>
        <p class="orders-lookt4"><?php echo $model->intro?></p>
    </div>


    <h4 class="orders-newtade">其他信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">所属分类</p>
        <p class="orders-lookt2"><?php echo $model->cat_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">负责人</p>
        <p class="orders-lookt2"><?php echo $model->principal_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">顺序</p>
        <p class="orders-lookt2"><?php echo $model->sort?></p>
    </div>

    <h4 class="orders-newtade">系统信息</h4>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建人</p>
        <p class="orders-lookt2"><?php echo $model->add_user_name?></p>
    </div>
    <div class="orders-look clearfix">
        <p class="orders-lookt1">创建时间:</p>
        <p class="orders-lookt2"><?php echo Yii::$app->formatter->asDate($model->create_time, 'php:Y-m-d')?></p>
    </div>
    <div class="orders-lookbut">
        <a href="<?=Url::to(['goods/index'])?>">
            <button class="orders-lookut" type="button">返回</button>
        </a>
    </div>
</div>