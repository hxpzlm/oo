<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->title = '库存预警信息列表';
$user = Yii::$app->user->identity;
$this->registerCssFile('@web/statics/css/stocksCheck.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/stocksCheck.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/statistics.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <input type="text" id="goods_name" name="goods_name" placeholder="请输入商品中英文名" value="<?=Yii::$app->request->get('goods_name')?>"/>
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'warning/create')){?>
        <span class="seeks-x2"><a href="<?=Url::to(['warning/create','store_id'=>Yii::$app->request->get('store_id')])?>"><i class="iconfont">&#xe603;</i>预警设置</a></span>
        <?php }?>
        <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id,'warning/index')){?>
            <span><a href="<?=Yii::$app->request->getUrl()?>&action=export"><i class="iconfont">&#xe60a;</i>导出表格</a></span>
        <?php }?>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>预警仓库</p>
            <select name="warehouse_id">
                <option value="">请选择</option>
                <?php foreach($ware as $v){?>
                    <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php }?>
            </select>
        </div>
        <? if(Yii::$app->user->identity->store_id==0){
            $store = \frontend\models\Store::findAll(['status'=>1]);
            ?>
            <div class="seeks-boxs clearfix">
                <p>入驻商家</p>
                <select name="store_id">
                    <option value="">请选择入驻商家</option>
                    <?php foreach($store as $val){?>
                        <option value="<?php echo $val['store_id'];?>" <?php if($val['store_id']==Yii::$app->request->get('store_id')){echo 'selected';};?>><?php echo $val['name'];?></option>
                    <?php }?>
                </select>
            </div>
        <?php } ?>
        <div class="seeks-boxs clearfix">
            <p>预警时间</p>
            <input type="text" name="time_start" class="start_rl" placeholder="请选择开始日期"  value="<?=Yii::$app->request->get('time_start')?>"/>-
            <input type="text" name="time_end" class="end_rl" placeholder="请选择结束日期"  value="<?=Yii::$app->request->get('time_end')?Yii::$app->request->get('time_end'):date('Y-m-d', time())?>" />
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <table class="orders-info">
        <tr>
            <th>预警信息</th>
            <th>预警阀值</th>
            <th>预警时间</th>
            <th>短信已发送</th>
            <th>关闭方式</th>
            <th>操作</th>
        </tr>
        <?php foreach ($model as $v){?>
        <tr>
            <td class="table-tdw"><?=$v['info']?></td>
            <td><?=$v['warning_num']?></td>
            <td><?=date('Y-m-d H:i:s',$v['warning_time'])?></td>
            <td><?=($v['is_send']==1)?"是":"否"?></td>
            <td><?php if($v['close_type']>0) echo ($v['close_type']==1)?"系统关闭":"手工关闭";else echo "　";?></td>
            <td >
               <?php if($v['close_type']>0){?>
              <i class="iconfont icon_1 del_icon">&#xe605;</i>
                <?php }else{?>
                <i class="iconfont icon_1 del_icon"> &#xe611;</i>
                <?php }?>
            </td>
        </tr>
            <!--确认和删除弹窗-->
            <div class="window_1">
                <?php if($v['close_type']>0){?>
                <p>确认要删除该条预警信息吗？</p>
                <a href="<?=Url::to(['warning/delete','id'=>$v['id']])?>" data-method="post"><button class="button button_1">确认</button></a>
                <?php }else{?>
                <p>确认手工关闭该条预警信息吗？</p>
                <a href="<?=Url::to(['warning/handle','id'=>$v['id']])?>" data-method="post"> <button class="button button_1">确认</button></a>
                <?php }?>
                <button class="button">取消</button>
            </div>

        <?php } ?>
    </table>
    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pagination,
    ]);?>
</div>
<!--内容end-->
<!--弹窗-->
<!--确认和删除弹窗-->
<!--<div class="window_1">-->
<!--    <p>确认该盘点单？确认后将无法修改！</p>-->
<!--    <button class="button button_1">确认</button>-->
<!--    <button class="button">取消</button>-->
<!--</div>-->
<?php \frontend\components\JsBlock::begin()?>
    <script>
        $(function(){
            $('#goods_name').autocomplete({
                minLength:0,
                source: [
                    <?php foreach (\frontend\components\Search::SearchGoods() as $v){?>
                    "<?=$v['name']?>",
                    <?php } ?>
                ]
            });
            $('#goods_name').focus(function(){
                if($(this).val() == ""){
                    $('#goods_name').autocomplete("search", "");
                }
            });
        });
    </script>
<?php \frontend\components\JsBlock::end()?>