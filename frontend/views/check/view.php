<?php

/* @var $this yii\web\View */

$this->title = '库存盘点-查看';
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_global/global.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/stocksCheck.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/stocksCheck.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--库存盘点跳转至查看页面-->
<div class="view">
    <div>
        <p class="view_p1"><span>盘点单号：</span><?=$model->check_no?></p>
        <p  class="title">仓库和商品信息</p>
        <p class="view_p1"><span>仓库：</span><?=$model->warehouse_name?></p>
        <p  class="title">所选商品批次盘点信息</p>
    </div>
    <ul>
        <?php
            $check_goods=(new \yii\db\Query())->from(Yii::$app->getDb()->tablePrefix.'check_goods')->where('check_id='.$model->check_id)->orderBy(['id'=>SORT_ASC])->all();
        foreach($check_goods as $v){
        ?>
        <li>
            <p>
                <lable class="view_lable1">
                    <?=$v['goods_name'].' '.$v['spec']?>
                </lable>
            </p>
            <p>
                <lable class="view_lable2">
                    <span><?=$v['batch_num']?></span>
                    <span>库存：<?=$v['stocks_num'].$v['unit_name']?></span>
                    <span>盘点：<?=$v['check_num'].$v['unit_name']?></span>
                    <span>盈亏：<?=$v['check_num']-$v['stocks_num']?></span>
                    <span><?=$v['remark']?></span>
                </lable>
            </p>
        </li>
<?php }?>
    </ul>
    <div>
        <p  class="title">系统信息</p>
        <p class="view_p1 view_p2"><span>开单人：</span><?=$model->add_user_name?></p>
        <p class="view_p1"><span>创建时间：</span><?=date('Y-m-d H:i:s', $model->create_time)?></p>
        <p class="view_p1 view_p2"><span>确认人：</span><?=$model->confirm_user_name?></p>
        <p class="view_p1 "><span>确认时间：</span><?=($model->confirm_time>0)?date('Y-m-d H:i:s', $model->confirm_time):''?></p>
    </div>
    <a href="<?=Url::to(['check/index'])?>" class="return">返回</a>
</div>
