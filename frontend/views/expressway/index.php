<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\Expressway */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物流公司管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/jquery-1.10.1.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/popup.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/purchaseOrders.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);
$expressway_info = \frontend\components\Search::SearchExpressway();
?>

<div class="container">
    <div class="seeks clearfix">
        <?php echo $this->render('_search', ['model' => $model]); ?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'expressway/create')==true){;?>
            <a href="<?=Url::to(['expressway/create'])?>"><span class="seeks-x2"><i class="iconfont">&#xe604;</i></I>新建物流公司</span></a>
        <?}?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'expressway/update')==true){?>
            <span onclick="javascript:$('#sort').submit()"><i class="iconfont">&#xe60e;</i></I>确认排序</span>
        <?php }?>
    </div>
    <?=Html::beginForm(Url::to(['expressway/index']),'post',['id'=>'sort'])?>
    <table class="orders-info">
        <tr>
            <th width="5%">顺序</th>
            <th>物流公司名称</th>
            <th>状态</th>
            <th>备注说明</th>
            <th>操作</th>
        </tr>
        <?php foreach($countries as $item):?>

            <tr>
                <td align="left" width="5%"><input type="text" name="sort[<?=$item['delivery_id']?>]" value="<?echo $item['sort']?>"></td>
                <td width="15%"><? echo $item['name'] ?></td>
                <td width="5%"><?php if($item['status']==1){echo '正常';}else{echo '停用';} ?></td>
                <td><? echo $item['remark'] ?></td>
                <td align="right" width="10%">
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'expressway/delete')==true){;?>
                        <?=Html::a('<i class="iconfont">&#xe605;</i>',['expressway/delete','id'=>$item['delivery_id']],
                            ['class'=>'orders-infosc',
                                'data'=>[ 'confirm' => '您确定要删除这条记录吗？删除后不可恢复！',
                                    'method' => 'post',],
                            ])?>
                    <?php }?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'expressway/update')==true){?>
                        <a href="<?=Url::to(['expressway/update','id'=>$item['delivery_id']])?>"><i class="iconfont">&#xe603;</i></a>
                    <?php } ?>
                    <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'expressway/view')==true){?>
                        <a href="<?=Url::to(['expressway/view','id'=>$item['delivery_id']])?>"><i class="iconfont">&#xe60b;</i></a>
                    <?php }?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
    <?=Html::endForm();?>

    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);;?>
</div>
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $("input[name='name']").bigAutocomplete({
                width:510,data:[
                    <?php foreach($expressway_info as $v){?>
                    {title:"<?=$v['name']?>"},
                    <?php }?>
                ]
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>