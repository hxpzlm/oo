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

$this->title = '客户列表';
$this->params['breadcrumbs'][] = $this->title;
$customers = \frontend\components\Search::SearchCustomers();
?>
<!--内容-->
<div class="container">
    <div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'customers/create')){?>
            <a href="<?=Url::to(['customers/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建客户</span></a>
        <?php }?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'customers/update')){?>
            <span onclick="javascript:$('#sort_l').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
    </div>
    <?=Html::beginForm(Url::to(['customers/index']),'post',['id'=>'sort_l']);?>
    <table class="orders-info">
        <tr>
            <th>顺序</th>
            <th>客户账号</th>
            <th>姓名</th>
            <th>性别</th>
            <th>联系电话</th>
            <th>Email/QQ/其他</th>
            <th>客户类型</th>
            <th>客户来源</th>
            <th>收货信息数目</th>
            <th>操作</th>
        </tr>
        <?php foreach($dataProvider as $item){ ?>
            <tr>
                <td class="table-left" width="8%">&nbsp;<input type="text" name="sort[<?=$item['customers_id']?>]" value="<?=$item['sort']?>"></td>
                <td class="table-left"><?= $item['username']; ?> </td>
                <td width="8%"><?= $item['real_name']; ?></td>
                <td width="10%"><?= ($item['sex']==2)?'保密':($item['sex']==1 ? '男' : '女'); ?></td>
                <td width="8%"><?= $item['mobile']; ?></td>
                <td width="15%" class="table-left"><?= $item['other']; ?></td>
                <td width="8%"><?= $item['type']==1 ? '企业客户' : '个人客户'; ?></td>
                <td width="8%">
                    <?php
                    $v = (new \yii\db\Query())->select('name')->from(Yii::$app->getDb()->tablePrefix.'shop')->where('shop_id='.$item['shop_id'])->one();
                    echo $v['name'];
                    ?>
                </td>
                <td width="10%"><?php echo (new \yii\db\Query())->select('address_id')->from(Yii::$app->getDb()->tablePrefix.'address')->where('customers_id='.$item['customers_id'])->count();?>
                </td>
                <td width="5%">
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'customers/delete')){?>
                        <a class="orders-infosc" href="javascript:;" nctype="<?=$item['customers_id']?>"><i class="iconfont">&#xe605;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'customers/update')){?>
                        <a href="<?=Url::to(['customers/update','id' => $item['customers_id']])?>"><i class="iconfont">&#xe603;</i></a>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'customers/view')){?>
                        <a href="<?=Url::to(['customers/view','id' => $item['customers_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                    <?php }?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?=Html::endForm(); ?>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pages,
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
            //客户帐号搜索
            $("input[name='CustomersSearch[username]']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($customers as $v){?>
                    {title:"<?=$v['username']?>"},
                    <?php }?>
                ],
                callback:function(data){
                    $(".close_btn img").show();
                    $(".close_btn img").click(function(){
                        $("input[name='CustomersSearch[username]']").val('');
                        $(this).hide();
                    })
                }
            });
            //删除
            var sc;
            $('.orders-infosc').click(function(){
                var id = $(this).attr('nctype');
                $('.orders-sct3>a').attr('href','<?=Url::to(['customers/delete'])?>&id='+id);
                sc = $(".orders-sc").bPopup();
            })
            $(".orders-sct1 i,.orders-sct3 span").click(function(){
                sc.close();
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>