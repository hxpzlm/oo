<?php

/* @var $this yii\web\View */

use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
		laydate({elem: '#ex-date'});//绑定元素
		laydate({elem: '#pur-date'});
	}();", \yii\web\View::POS_END);

$this->title = '退货订单-编辑';
$this->params['breadcrumbs'][] = ['label' => 'refuse-order', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refuse_id, 'url' => ['view', 'id' => $model->refuse_id]];
$this->params['breadcrumbs'][] = 'Update';

$query = new \yii\db\Query();
//获取仓库
$warehose = $query->select('warehouse_id,name')->from('s2_warehouse')->all();
//获取负责人
$principal = $query->select('user_id,username')->from('s2_user')->where('status=1')->all();

?>

<!--内容-->
<div class="container">
    <?= $this->render('_form', [
        'model' => $model,
        'rog_model'  => $rog_model,
    ]) ?>
</div>