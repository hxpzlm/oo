<?php
/**
 * Created by xiegao.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 14:23
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
AppAsset::register($this);
$this->registerCssFile('@web/statics/css/css_plug/laydate.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/sellDe.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/css_plug/autocomplete.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/laydate.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/sellDe.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/autocomplete.js',['depends'=>['yii\web\YiiAsset']]);

$this->registerJsFile('@web/statics/css/css_global/global.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/css/purchaseOrders.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);

$this->registerJsFile('@web/statics/js/js_plug/jquery.form.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerJs("!function(){
		laydate({elem: '#start-date'});//绑定元素
		laydate({elem: '#end-date'});
	}();", \yii\web\View::POS_END);
$this->title = '销售出库';
$this->params['breadcrumbs'][] = $this->title;
$query = new \yii\db\Query();
if(Yii::$app->user->identity->store_id>0){
    $store_id=" and store_id=".Yii::$app->user->identity->store_id;
}else{
    $store_id="";
}
$warehose = $query->select('warehouse_id,name')->from('s2_warehouse')->where('status=1 '.$store_id)->all();
//获取负责人
$shop = $query->select('shop_id,name')->from('s2_shop')->where('status=1 '.$store_id)->all();
$delivery = $query->select('delivery_id,name')->from('s2_expressway')->where('status=1 '.$store_id)->all();
$order = \frontend\components\Search::SearchOrder();
$goods_list = \frontend\components\Search::SearchGoods();
?>
<!--笼罩层-->
<div class="iDiv"></div>

<div class="container">
    <?php $form = ActiveForm::begin([
        'action' => ['comfirm'],
        'method' => 'get',
    ]); ?>
    <div class="seeks clearfix">
        <div class="close_btn"><input type="text" placeholder="请输入订单编号" name="order_no" value="<?=Yii::$app->request->get('order_no')?>" autocomplete="off"/><img src="statics/img/close_icon.jpg" class="img_css"></div>
        <input type="hidden" name="store_id" value="<?=Yii::$app->user->identity->store_id?>">
        <?= Html::submitButton('<i class="iconfont">&#xe60d;</i>搜索') ?>
        <p class="seeks-xl">更多搜索条件<label>▼</label></p>
        <span class="seeks-x2"><i class="iconfont">&#xe613;</i></I>批量出库</span>
        <span><a href="<?=Yii::$app->request->getUrl();?>&action=export"><i class="iconfont">&#xe60a;</i></I>导出表格</a></span>
    </div>
    <div class="seeks-box clearfix">
        <div class="seeks-boxs clearfix">
            <p>收货人</p>
            <input type="text" name="accept_name" placeholder="" value="<?=Yii::$app->request->get('accept_name')?>"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>物流单号</p>
            <input type="text" name="accept_name" placeholder="" value="<?=Yii::$app->request->get('accept_name')?>"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>商品名称</p>
            <input type="text" name="goods_name" placeholder="" value="<?=Yii::$app->request->get('goods_name')?>"/>
        </div>
        <div class="seeks-boxs clearfix">
            <p>销售平台</p>
            <select name="shop">
                <option value="">请选择</option>
                <?php foreach($shop as $v){?>
                    <option value="<?php echo $v['shop_id'];?>" <?php if($v['shop_id']==Yii::$app->request->get('shop_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
        <div class="seeks-boxs seeks-boxst1 clearfix">
            <p>仓库</p>
            <select name="warehouse_id">
                <option value="">请选择</option>
                <?php foreach($warehose as $v){?>
                    <option value="<?php echo $v['warehouse_id'];?>" <?php if($v['warehouse_id']==Yii::$app->request->get('warehouse_id')){echo 'selected';};?>><?php echo $v['name'];?></option>
                <?php };?>
            </select>
        </div>
        <div class="seeks-boxs clearfix">
            <p>是否出库</p>
            <select name="status">
			<?php $status =Yii::$app->request->get('status');?>
            <option value="" <?=empty($status)?'selected="selected"':''?>>请选择</option>
            <option value="1" <?=($status==1)?'selected="selected"':''?>>是</option>
            <option value="2" <?=($status==2)?'selected="selected"':''?>>否</option>
            </select>
        </div>
        <div class="seeks-boxs seeks-boxst2 clearfix">
            <p>销售日期</p>
            <input type="text" placeholder="销售开始日期" name="order_time_start" id='start-date' class="laydate-icon" value="<?=Yii::$app->request->get('order_time_start')?>"/>
            <span>-</span>
            <input type="text" placeholder="销售终止日期" id='end-date' name="order_time_end" class="laydate-icon" value="<?=Yii::$app->request->get('order_time_end')?>"/>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <table class="orders-info">
        <tr>
            <th>仓库</th>
            <th>销售平台</th>
            <th>订单编号</th>
			<th>收货人姓名</th>
            <th>商品中英文名称（含规格）</th>
            <th>商品数量</th>
            <th>实收款</th>
            <th>销售日期</th>
            <th>出库状态</th>
            <th>出库日期</th>
            <th>物流单号</th>
            <th>操作</th>
        </tr>
        <?php foreach($dataProvider as $item){?>
        <tr>
            <td width="7%" class="table-left">&nbsp;<?=$item['warehouse_name']?></td>
            <td width="5%"><?=$item['shop_name']?></td>
            <td width="7%"><?=$item['order_no']?></td>
			<td><?=$item['accept_name']?></td>
            <td class="table-left">
            <?php foreach($goods[$item['order_id']] as $v){?>
            <p><span><?=$v['goods_name']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$v['spec']?></span></p>
            <?php }?>
            </td>
            <td width="5%">
            <?php foreach($goods[$item['order_id']] as $v){?>
                    <p><span><?=$v['number']?><?=$v['unit_name']?></span></p>
             <?php }?>
            </td>
            <td width="7%" class="table-right"><?=$item['real_pay']?>元</td>
            <td width="7%"><?=($item['sale_time']>0)?date('Y-m-d',$item['sale_time']):"";?></td>
            <td width="5%"><?=($item['delivery_status']==1)?"是":"否";?></td>
            <td width="7%"><?=($item['confirm_time']>0)?date('Y-m-d',$item['confirm_time']):"";?></td>
            <td width="10%"><?=empty($item['delivery_code'])?"":$item['delivery_code'];?></td>
            <td width="5%">
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'sale/handle')){?>
                <div class="sellDe-del"><i class="iconfont sellDe-deli <?php if($item['delivery_status']==0){?>icon-queren<?php }else{?>icon-quxiao<?php }?>" title="<?=($item['delivery_status']==1)?"取消确认出库":"确认出库";?>"></i>
                    <!--确认入库操作-->
                    <div class="sellDe-delbox">
                       <?=Html::beginForm(Url::to(['sale/handle','order_id'=>$item['order_id'],'action'=>'confirm']),'post',['id'=>'form'])?>
                        <input type="hidden" name="order_id" value="<?=$item['order_id']?>">
                        <p class="sd-delboxt1_1"><i class="iconfont">&#xe608;</i></p>
                        <p class="sd-delboxt1">请输入物流信息</p>
                        <div class="sd-delboxt2">
                            <span>物流公司：</span>
                            <select name="delivery_id" id="selector">
                                <option value="">请选择</option>
                                <?php foreach($delivery as $ex){?>
                                <option value="<?=$ex['delivery_id']?>" <?php if($item['delivery_id']>0 && $item['delivery_id']==$ex['delivery_id']) echo "selected='selected'";?>><?=$ex['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="sd-delboxt2">
                            <span>物流单号：</span>
                            <input class="danhao" type="text" name="delivery_code"/>
                        </div>
                        <div class="sd-delboxt3">
                            <span class="sd-delboxt3-x">确认</span>
                            <span class="sd-delboxt3-s">取消</span>
                        </div>
                        <?=Html::endForm();?>
                    </div>
                    <!--确认取消出库操作-->
                    <div class="sellDe-delbox2">
                        <p class="sd-delboxt1_1"><i class="iconfont">&#xe608;</i></p>
                        <p class="delbox2t1">确认取消该销售出库操作？</p>
                        <div class="delbox2t2">
                            <a href="<?=Url::to(['sale/handle','order_id'=>$item['order_id'],'action'=>'cancle'])?>" data-method="post"><span class="delbox2t2-x">确认</span></a>
                            <span class="delbox2t2-s">取消</span>
                        </div>
                    </div>
                </div>
                <?php }?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'sale/view')){?><a href="<?=Url::to(['sale/view','id'=>$item['order_id']])?>" data-method='post'><i class="iconfont">&#xe60b;</i></a><?php }?>
            </td>
        </tr>
        <?php }?>
    </table>
    <?php echo \frontend\components\PageWidget::widget([
        'pagination' => $pages,
    ]);?>

</div>

<!--批量导入弹窗第一步-->
<div class="window_1">
    <p class="window_1_p1">
        <span>批量出库数据导入</span>
        <i class="iconfont icon">&#xe608;</i>
    </p>
    <input class="window_1_text" type="text" />
    <div class="button">
        <input class="window_1_file" id="fileupload" name="orderfile" type="file"/>浏览
    </div>
    <button class="button button_1">导入</button>
    <p class="window_1_p2">
        模板下载：<a href="statics/svg/xsckpldrmb.xls">销售出库批量导入模板</a>
    </p>
    <p class="window_1_p2">
        注意：请严格按照导入模板说明整理销售出库数据！
    </p>
    <div class="clear"></div>
</div>
<!--批量导入弹窗第二步-->
<div class="window_2">
    <img src="statics/img/loding.gif"/>
    <p>注意：请严格按照导入模板说明整理销售出库数据！</p>
</div>
<!--批量导入弹窗第三步-->
<div class="window_3">
    <p class="window_3_p1">
        <span>批量出库数据导入</span>
        <i class="iconfont icon">&#xe608;</i>
    </p>
    <p class="window_3_p2">导入结果：</p>
    <p class="window_3_p3">
        导入成功495条记录！<br />
        导入失败5条记录，行号为：5,7,9,20,50！请核对修正这些数据后再单独导入！
    </p>
    <button>关闭</button>
    <div class="clear"></div>
</div>


<?php \frontend\components\JsBlock::begin()?>
<script type="text/javascript">
$(function () {
    var bar = $('.bar');
    var percent = $('.percent');
    var showimg = $('#showimg');
    var progress = $(".progress");
    var files = $(".files");
    var btn = $(".button_1");
    $("#fileupload").wrap("<form id='myupload' action='<?=Url::to(['sale/load'])?>' method='post' enctype='multipart/form-data'></form>");
    $(".button_1").click(function(){
        $("#myupload").ajaxSubmit({
            dataType:  'json',
            beforeSend: function() {
                showimg.empty();
                progress.show();
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
                //btn.html("上传中...");
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal);
                percent.html(percentVal);
            },
            success: function(data) {
                if(data.status==1){
                    alert(data.msg);
                    return false;
                }else if(data.status==2){
                    alert(data.msg);
                    return false;
                }else if(data.status==3){
                    alert(data.msg);
                    return false;
                }else if(data.status==4){
                    alert(data.msg);
                    return false;
                }else{
                    $('.window_3_p3').html('导入成功'+data.msg.t_total+'条记录！<br />导入失败'+data.msg.f_total+'条记录，行号为：'+data.msg.f_line+'！请核对修正这些数据后再单独导入！');
                    $('.window_1_text').val('');
                    $('.window_1_file').val('');
                    $('.window_1').hide();
                    $('.window_2').show();
                    var animate_1 = setTimeout(function(){
                        $('.window_2').hide();
                        $('.window_3').show();
                    },1000);
                }

            },
            error:function(xhr){
                btn.html("上传失败");
                bar.width('0')
                files.html(xhr.responseText);
            }
        });
    });
});
</script>

<script>

    $(function(){
        $('#selector').change(function(){
			if(!$(this).val()){
				alert("物流公司不能为空");
			}
		});
        $('.sd-delboxt3-x').click(function(){
			var code = $(this).parent().siblings('.sd-delboxt2').find('.danhao').val();
            if(!code){ alert("订单号不能为空");return false;}
            $(this).parent().parent('#form').submit();
        });
        //订单编号数据过滤
        $("input[name='order_no']").bigAutocomplete({
            width:510,data:[
                <?php foreach($order as $v){?>
                {title:"<?=$v['order_no']?>"},
                <?php }?>
            ],
            callback:function(data){
                $(".close_btn img").show();
                $(".close_btn img").click(function(){
                    $("input[name='order_no']").val('');
                    $(this).hide();
                })
            }
        });
        //商品名称过滤
        $("input[name='goods_name']").bigAutocomplete({
            width:400,data:[
                <?php foreach($goods_list as $v){?>
                {title:"<?=$v['name']?>"},
                <?php }?>
            ]
        });
    });
</script>
<?php \frontend\components\JsBlock::end()?>
