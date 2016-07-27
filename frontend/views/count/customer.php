<?php

/* @var $this yii\web\View */

$this->title = '客户交易统计';
use yii\helpers\Html;

use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/statistics.js',['depends'=>['yii\web\YiiAsset']]);
$wh['status']=1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id']=Yii::$app->user->identity->store_id;
}else{
    if(!empty(Yii::$app->request->get('store_id')))
        $wh['store_id']=Yii::$app->request->get('store_id');
}
$user = \common\models\User::find()->asArray()->select('username')->where($wh)->all();
$shop = \frontend\models\Shop::find()->asArray()->select('shop_id,name')->where($wh)->all();
?>
<!--内容-->
<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['customer'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" id="user_name" name="user_name" placeholder="请直接选择或输入客户账号" value="<?=Yii::$app->request->get('user_name')?>" autocomplete="off"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'count/erp')){?>
            <span class="seeks-x2"><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php };?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>销售平台</p>
            <select name="shop_id">
                <option value="">请选择</option>
                <?php foreach($shop as $v){?>
                    <option value="<?php echo $v['shop_id'];?>" <?php if($v['shop_id']==Yii::$app->request->get('shop_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
        <?php if(Yii::$app->user->identity->store_id==0){
            $store = \frontend\models\Store::findAll(['status'=>1]);
            ?>
            <div class="seeks-boxs clearfix">
                <p>入驻商家</p>
                <select name="store_id">
                    <option value="">请选择</option>
                    <?php foreach($store as $v){?>
                        <option value="<?php echo $v['store_id'];?>" <?php if($v['store_id']==Yii::$app->request->get('store_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                    <?php };?>
                </select>
            </div>
        <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>
    <table class="orders-info">
        <tr>
            <th>客户账号</th>
            <th>客户姓名</th>
            <th>联系电话</th>
            <th>销售平台</th>
            <th>本月购买次数</th>
            <th>本月购买金额</th>
            <th>累计购买次数</th>
            <th>累计购买金额(元)</th>
            <th>累计退货次数</th>
            <th>累计退货金额(元)</th>

        </tr>
        <?php foreach ($model as $v){?>
            <tr>
                <td><?=$v['customer_name']?></td>
                <td><?=$v['real_name']?></td>
                <td><?=$v['mobile']?></td>
                <td><?=$v['shop_name']?></td>
                <td><?=$v['number']?></td>
                <td><?=$v['amount']?></td>
                <td><?=$v['nums']?></td>
                <td><?=$v['amounts']?></td>
                <td><?=($v['rnums']>0)?$v['rnums']:'0'?></td>
                <td><?=($v['ramounts']>0)?$v['ramounts']:'0.00'?></td>

            </tr>
        <?php } ?>
    </table>
    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
        $('#user_name').autocomplete({
            minLength:0,
            source: [
                <?php foreach ($user as $v){?>
                "<?=$v['username']?>",
                <?php } ?>
            ]
        });
        $('#user_name').focus(function(){
            if($(this).val() == ""){
                $('#user_name').autocomplete("search", "");
            }
        });
    });
</script>
<?php \frontend\components\JsBlock::end()?>
