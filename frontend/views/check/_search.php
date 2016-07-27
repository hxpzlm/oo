<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\PurchaseSearch */
/* @var $form yii\widgets\ActiveForm */

$user = new User();
$userinfo = $user->findIdentity(Yii::$app->session['__id']);
if(!empty($userinfo['store_id'])){
    $s_store_id = ' and store_id='.$userinfo['store_id'];
}else{
    $s_store_id = '';
}

//获取仓库
$warehose = (new \yii\db\Query())->select('warehouse_id,name')->from(Yii::$app->getDb()->tablePrefix.'warehouse')->where('status=1'.$s_store_id)->all();
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="seeks clearfix">
        <div class="close_btn"><input type="text" name="warehouse_name" placeholder="请直接选择或输入选择商品仓库" value="<?=Yii::$app->request->get('warehouse_name')?>"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label><?php if(strtotime(Yii::$app->request->get('create_time_start'))>strtotime(Yii::$app->request->get('create_time_end'))){?>&nbsp;&nbsp;<strong style="color: red; margin: 0; background: none;">开始时间不能大于结束时间</strong><?php };?></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'check/create')){?><a href="<?=Url::to(['check/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建盘点单</span></a><?php };?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'check/view')){?>
            <span><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php }?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>商品名称</p>
            <input type="text" name="goods_name" style="width: 390px;" value="<?=Yii::$app->request->get('goods_name')?>" />
        </div>
        <div class="seeks-boxs clearfix">
            <p>开单人</p>
            <input type="text" name="add_user_name" value="<?=Yii::$app->request->get('add_user_name')?>" />
        </div>
        <div class="seeks-boxs seeks-boxst1 clearfix">
            <p>盘点完成</p>
            <select name="from_warehouse_id">
                <option value="">请选择</option>
                <option value="2" <?php if(2==Yii::$app->request->get('status')){echo 'selected';};?>>否</option>
                <option value="1" <?php if(1==Yii::$app->request->get('status')){echo 'selected';};?>>是</option>
            </select>
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
        <div class="seeks-boxs seeks-boxst2 clearfix">
            <p>开单日期</p>
            <input type="text" placeholder="开单开始日期" name="create_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('create_time_start')?>"/>
            <span>-</span>
            <input type="text" placeholder="开单终止日期" name="create_time_end" id='end-date' class="laydate-icon" value="<?=Yii::$app->request->get('create_time_end')?Yii::$app->request->get('create_time_end'):date('Y-m-d', time())?>"/>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $("input[name='warehouse_name']").bigAutocomplete({
                width:510,
                data:[
                    <?php foreach($warehose as $v){?>
                    {title:"<?=$v['name']?>",result:{warehouse_id:"<?=$v['warehouse_id']?>"}},
                    <?php }?>
                ],
                callback:function(data){
                    $("input[name='warehouser_id']").val(data.result.warehouse_id);
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='warehouse_name']").val('');
                        $(this).hide();
                    })
                }
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>