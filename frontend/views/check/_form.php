<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
AppAsset::register($this);

$this->registerCssFile('@web/statics/css/css_global/global.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/stocksCheck.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);

$this->registerJsFile('@web/statics/js/stocksCheck.js',['depends'=>['yii\web\YiiAsset']]);
/* @var $this yii\web\View */
/* @var $model frontend\models\Check */
/* @var $form yii\widgets\ActiveForm */
$tablePrefix = Yii::$app->getDb()->tablePrefix;
$act = Yii::$app->request->get('step');
$step = !empty($act)?$act:1;
$store_id = Yii::$app->user->identity->store_id;
$where['status'] = 1;
if($store_id>0){
    $where['store_id'] = $store_id;
}
//获取仓库
$warehose = (new \yii\db\Query())->select('warehouse_id,name')->from(Yii::$app->getDb()->tablePrefix.'warehouse')->where($where)->all();
$warehose_row = array();
if(!empty($warehose)){
    $warehose_row[''] = '请选择';
    foreach($warehose as $value){
        $warehose_row[$value['warehouse_id']] = $value['name'];
    }
}
//查询分类信息
$ow=array();$ow['status']=1;
if($store_id>0) $ow['store_id']=$store_id;
$ow['parent_id'] = 0;
$category=(new \yii\db\Query())->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where($ow)->orderBy(['sort'=>SORT_ASC])->all();
if($category){
    $cat_row = array();
    $cat_row[''] = '请选择';
    foreach($category as $v1){
        $cat_row[$v1['cat_id']] = $v1['name'];
        $category2=(new \yii\db\Query())->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where('parent_id='.$v1['cat_id'])->orderBy(['sort'=>SORT_ASC])->all();
        if(!empty($category2)){
            foreach($category2 as $v2){
                $cat_row[$v2['cat_id']] = '┗━'.$v2['name'];
                $category3=(new \yii\db\Query())->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where('parent_id='.$v2['cat_id'])->orderBy(['sort'=>SORT_ASC])->all();
                if(!empty($category3)){
                    foreach($category3 as $v3){
                        $cat_row[$v3['cat_id']] = '┗━━'.$v3['name'];
                    }
                }
            }
        }

    }
}

$cg = Yii::$app->session['check_step1']['cg'];
?>

<?php if($step==3){?>
<!--步骤三-->
    <?php $form = ActiveForm::begin(); ?>
    <p class="step step_3">
        <span>步骤一：选择需盘点的仓库和商品</span>
        <span>步骤二：确定所选商品批次</span>
        <span class="step_3_3">步骤三：输入盘点信息并完成</span>
    </p>
    <p class="titile">所选商品批次信息</p>
    <ul class="step_3_ul">
        <?php
        foreach($cg as $v){

            $check_arr = array();
            $remark_arr = array();
            //显示数据库已有的值
            if(!empty($model->check_id)){
                $sr_map['check_id'] = $model->check_id;
                $sr_map['goods_id']  = $v['goods_id'];
                $sr_data = (new \yii\db\Query())->select('check_num,remark')->from($tablePrefix.'check_goods')->where($sr_map)->one();
                $check_arr = explode(',', $sr_data['check_num']);
                $remark_arr = explode(',', $sr_data['remark']);
            }
        ?>
        <li>
            <p class="step_3_ul_p1"><?=$v['goods_name'].' '.$v['spec']?></p>
            <?php
            $goods_list = Yii::$app->session['check_step2'][$v['goods_id']];
            foreach($goods_list as $k1=>$v1){
            ?>
            <p class="step_3_ul_p2">
                <span><?=$v1['batch_num']?><input type="hidden" name="CheckGoods[batch_num][<?=$v['goods_id']?>][]" value="<?=$v1['batch_num']?>" /></span>
                <input type="hidden" name="CheckGoods[stocks_num][<?=$v['goods_id']?>][]" value="<?=$v1['stock_num']?>" />
                <span class="zh_number"><?=$v1['stock_num']?></span><?=$v1['unit_name']?>
				<span class="step_3_ul_span1">
					<input type="text" class="dut_number" name="CheckGoods[check_num][<?=$v['goods_id']?>][]" value="" />&nbsp;件
				</span>
                <span class="step_3_ul_span2"></span>
                <input class="step_3_ul_span3" type="text" placeholder="盘点该商品批次原因*" name="CheckGoods[remark][<?=$v['goods_id']?>][]" value="" />
            </p>
            <?php }?>
        </li>
        <?php }?>
    </ul>
    <input type="hidden" name="act" value="check_3" />
    <input class="button button1" type="submit" value="提交审核" />
    <?php ActiveForm::end(); ?>
    <a href="<?=Url::to(['check/create','step'=>2])?>"><button class="button button2">上一步</button></a>
    <div class="clear"></div>
<?php }elseif($step==2){?>
    <!--步骤二-->
    <?php $form = ActiveForm::begin(); ?>
    <p class="step step_2">
        <span>步骤一：选择需盘点的仓库和商品</span>
        <span class="step_2_2">步骤二：确定所选商品批次</span>
        <span>步骤三：输入盘点信息并完成</span>
    </p>
    <p class="titile">所选商品批次信息</p>
    <div class="table_box">
        <?php
        foreach($cg as $v){
        ?>

        <ul class="checkbox">
            <li class="first-child" style="width: 400px; text-align: left;" title="<?=$v['goods_name']?>">&nbsp;&nbsp;<?=mb_substr($v['goods_name'],0,30,'utf-8').' '.$v['spec']?></li>
            <?php
            $stocks_data = (new \yii\db\Query())->select('*')->from($tablePrefix.'stocks')->where('goods_id='.$v['goods_id'])->orderBy(['purchase_time'=>SORT_DESC]);
            $stocks_data->andWhere(['>','stock_num',0]);
            $stocks_data->andWhere(['!=','batch_num','']);
            $goods_list = $stocks_data->all();
            $checked = '';
            foreach($goods_list as $v1){
                if(!empty($model->check_id)){
                    //判断是否选中
                    $b_map['check_id'] = $model->check_id;
                    $b_map['goods_id']  = $v['goods_id'];
                    $batch_s = (new \yii\db\Query())->select('batch_num')->from($tablePrefix.'check_goods')->where($b_map)->one();
                    $batch_arr = explode(',', $batch_s['batch_num']);
                    if(in_array($v1['batch_num'], $batch_arr)){
                        $checked = 'checked="checked"';
                    }
                }

            ?>
            <li style="text-align: left;"><input type="checkbox" name="CheckGoods[batch_num][<?=$v['goods_id']?>][]" value="<?=$v1['batch_num']?>" <?=$checked?>/><?=$v1['batch_num']?>(<?=$v1['stock_num'].$v1['unit_name']?>)</li>
            <?php }?>
        </ul>

        <?php }?>
        <div class="clear"></div>
    </div>
    <input type="hidden" name="act" value="check_2" />
    <input class="button button1" type="submit" value="下一步" />
    <?php ActiveForm::end(); ?>
    <a href="<?=Url::to(['check/create','step'=>1])?>"><button class="button button2">上一步</button></a>
    <div class="clear"></div>
    <? }else{?>
    <!--步骤一-->
    <?php $form = ActiveForm::begin(); ?>
    <p class="step step_1">
        <span class="step_1_1">步骤一：选择需盘点的仓库和商品</span>
        <span>步骤二：确定所选商品批次</span>
        <span>步骤三：输入盘点信息并完成</span>
    </p>
    <p class="titile">仓库及商品信息</p>
    <div class="main_l">
        <div class="main_l_1"><p>仓库：</p></div>
        <div class="main_l_2"><p>商品选择：</p></div>
    </div>
    <div class="main_r">
        <?= $form->field($model, 'warehouse_id')->dropDownList($warehose_row,['class'=>'r_select1','style'=>'float:left; clear: both;','onchange'=>"$('#check-warehouse_name').val($('#check-warehouse_id option:selected').text())",'value'=>$model->warehouse_id])->label(false)->hint('<label>* 请选择要盘点的仓库。</label>') ?>
        <?= Html::activeHiddenInput($model,'warehouse_name',['value'=>$model->warehouse_name])?>
        <div class="main_mid">
            <?php echo $form->field($model,'cat_id')->DropDownList($cat_row,['class'=>'mid_select2'])->label(false)->hint(false);?>
            <!--仓库列表1 （左）-->
            <ul class="mid_product mid_product_1">
                <!--查询出来的内容-->
            </ul>

        </div>
        <div class="icon_rl">
            <span class="iconfont icon_right">&#xe61f;</span>
            <br /><br /><br /><br />
            <span class="iconfont icon_left">&#xe618;</span>
        </div>
        <div class="main_r_r">
            <!--仓库列表2 （右）-->
            <ul class="r_product r_product_1">
                <!--选中添加的内容-->
                <?php
                if(!empty($cg_model)){
                    foreach($cg_model as $item){
                        echo '<li class="jj" vs='.$model->cat_id.' id='.$item['goods_id'].' name='.$model->warehouse_id.' gn="'.$item['goods_name'].'" spec="'.$item['spec'].'" unit_id="'.$item['unit_id'].'" unit_name="'.$item['unit_name'].'" bn="'.$item['batch_num'].'" snum="'.$item['stocks_num'].'">'.$item['goods_name'].'</li>';
                        echo '<input type="hidden" name="CheckGoods[id][]" value='.$item['id'].'>';
                        echo '<input type="hidden" name="CheckGoods[goods_id][]" value='.$item['goods_id'].'>';
                        echo '<input type="hidden" name="CheckGoods[goods_name][]" value="'.$item['goods_name'].'">';
                        echo '<input type="hidden" name="CheckGoods[spec][]" value="'.$item['spec'].'">';
                        echo '<input type="hidden" name="CheckGoods[unit_id][]" value="'.$item['unit_id'].'">';
                        echo '<input type="hidden" name="CheckGoods[unit_name][]" value="'.$item['unit_name'].'">';
                        echo '<input type="hidden" name="CheckGoods[batch_num][]" value="'.$item['batch_num'].'">';
                        echo '<input type="hidden" name="CheckGoods[stocks_num][]" value="'.$item['stocks_num'].'">';
                    }
                }
                ?>
            </ul>
        </div>
        <span class="tip_2" style="float: left; font-weight: bold;"> * 请选择要盘点的商品。</span>
        <div class="clear"></div>
    </div>
    <input name="act" type="hidden" value="check_1" />
    <input class="button button1" type="submit" value="下一步" />
    <?php ActiveForm::end(); ?>
    <a href="<?=Url::to(['check/index'])?>"><button class="button button2">返回</button></a>
    <div class="clear"></div>
<?php }?>

<?php \frontend\components\JsBlock::begin()?>
<script>
   $(function(){
       var check_id = "<?=Yii::$app->request->get('id');?>";
       if(check_id!=''){
           stocksCheckAjax();
       }

        $('.r_select1').change(function(){
            vals = $('.r_select1').val(); //仓库id
            if(vals!=''){
                stocksCheckAjax();
            }
        });
        $('.mid_select2').change(function(){
            cid = $('.mid_select2').val(); //分类id
            if(cid!=''){
                stocksCheckAjax();
            }

        });

        //选中的数据特价样式
        //$('.mid_product_1 li').click(function(){
        $('.mid_product_1').on('click','.jj',function(){
            if($(this).attr('class') != 'select'){
                $(this).addClass('select');
            }else{
                $(this).removeClass('select');
            }
        });


        //默认显示对应的仓库内容
        defaultEffect();
        function defaultEffect(){
            var a_number = $('.mid_product_1 li').length;
            var xl_val = $('.r_select1').val();
            for(var i=0;i<a_number;i++){
                var ckName = $('.mid_product_1 li').eq(i).attr('name');
                if(ckName == xl_val){
                    $('.mid_product_1 li').eq(i).show();
                }else{
                    $('.mid_product_1 li').eq(i).hide();
                }
            }
        }

        //切换仓库时再次筛选数据
        $('.r_select1').change(function(){
            $('.mid_product_1 li').removeClass('select');
            $('.mid_select2').prop('value','allbrand');
            defaultEffect();
        });


        //点击向右侧添加数据
        $('.icon_right').click(function(){
            $('.mid_product_1 li.select').each(function(){
                $('.r_product_1').append($(this));
                var input = '<input type="hidden" name="CheckGoods[goods_id][]" value='+$(this).attr("id")+'>' +
                    '<input type="hidden" name="CheckGoods[goods_name][]" value="'+$(this).attr("gn")+'">' +
                    '<input type="hidden" name="CheckGoods[spec][]" value="'+$(this).attr("spec")+'">' +
                    '<input type="hidden" name="CheckGoods[unit_id][]" value='+$(this).attr("unit_id")+'>' +
                    '<input type="hidden" name="CheckGoods[unit_name][]" value="'+$(this).attr("unit_name")+'">' +
                    '<input type="hidden" name="CheckGoods[batch_num][]" value="'+$(this).attr("bn")+'">' +
                    '<input type="hidden" name="CheckGoods[stocks_num][]" value='+$(this).attr("snum")+'>';
                $('.r_product_1').append(input);
                $('.r_product_1 li.select').removeClass('select');
            });

            //双击向左侧添加
            $('.main_r_r').on('dblclick','.r_product li',function(){
                $('.mid_product_1').append($(this));
                defaultEffect();
            });
        });

        //向左侧移动数据
        $('.icon_left').click(function(){
            $('.r_product_1 li.select').each(function(){
                $('.mid_product_1').append($(this));
                defaultEffect();
                $('.mid_product_1 li.select').removeClass('select');
            });
        });


        $('.mid_select2').change(function(){
            sxFn();
        });

        function sxFn(){
            var thisVal = $('.mid_select2').val();
            var thisCk = $('.r_select1').val();
            if(thisVal != 'allbrand'){
                $('.mid_product_1 li').each(function(index){
                    var LinVal = $('.mid_product_1 li').eq(index).attr('vs');
                    var LinCk = $('.mid_product_1 li').eq(index).attr('name');
                    if(LinVal == thisVal && thisCk == LinCk){
                        $('.mid_product_1 li').eq(index).show();
                    }else{
                        $('.mid_product_1 li').eq(index).hide();
                    }
                });
            }else{
                $('.mid_product_1 li').show();
                defaultEffect();
            }

        }

        //双击向右侧添加
        //$('.mid_product_1 li').dblclick(function(){
        $('.mid_product_1').on('dblclick','.jj',function(){
            $('.r_product_1').append($(this));

            var input = '<input type="hidden" name="CheckGoods[goods_id][]" value='+$(this).attr("id")+'>' +
                '<input type="hidden" name="CheckGoods[goods_name][]" value="'+$(this).attr("gn")+'">' +
                '<input type="hidden" name="CheckGoods[spec][]" value="'+$(this).attr("spec")+'">' +
                '<input type="hidden" name="CheckGoods[unit_id][]" value='+$(this).attr("unit_id")+'>' +
                '<input type="hidden" name="CheckGoods[unit_name][]" value="'+$(this).attr("unit_name")+'">' +
                '<input type="hidden" name="CheckGoods[batch_num][]" value="'+$(this).attr("bn")+'">' +
                '<input type="hidden" name="CheckGoods[stocks_num][]" value='+$(this).attr("snum")+'>';
            $('.r_product_1').append(input);
            //双击向左侧添加
            $('.main_r_r').on('dblclick','.r_product li',function(){
                $('.mid_product_1').append($(this));
                defaultEffect();
            });
            defaultEffect();
        });

       var result = 0;
       $('.checkbox').each(function(index){
           var len = $(this).find('li').length;
           var sum = (len - 1) * 150 + 300;
           if(sum >= 1427){
               if(sum > result){
                   $('.checkbox').width(sum);
                   result = sum;
               }else{
                   $('.checkbox').width(result);
               }
           }else if(result < 1427){
               $('.checkbox').width(1427);
           }else{
               $('.checkbox').width(reulst);
           }
       });

    });

    function stocksCheckAjax(){

        var vals = $('.r_select1').val(); //仓库id
        var cid = $('.mid_select2').val(); //分类id

        //根据仓库id与分了id帅选
        $.post("./index.php?r=check%2Findex",{action:'goods_screen',warehouse_id:vals,cid:cid},function(res){
            for(var i=0; i<res.length; i++){

                $('.mid_product').append('<li class="jj" vs='+res[i].cat_id+' id='+res[i].goods_id+' name='+res[i].warehouse_id+' gn="'+res[i].goods_name+'" spec="'+res[i].spec+'" unit_id="'+res[i].unit_id+'" unit_name="'+res[i].unit_name+'" bn="'+res[i].batch_num+'" snum="'+res[i].stock_num+'">'+res[i].goods_name+'</li>');
            }
        },"json");
    }

   $(document).ready(function(){
       //当件数发生改变时触发
       $('.dut_number').change(function(){
           clacFn($(this));
       });
       $('.dut_number').keyup(function(){
           var tmptxt = $(this).val();
           if(tmptxt.replace(/\D|^0/g,'')){
               $(this).css('border-color','#CCCCCC');
           }else{
               $(this).css('border-color','red');
               $(this).val('');
           }
       });

       //计算剩余件数方法
       function clacFn(obj){
           var $this = obj.parent('.step_3_ul_span1');
           var sr_number = obj.val();
           var zh_number = $this.prev('.zh_number').val();
           var sum = zh_number - sr_number;
           if(sum >= 0){
               $this.next('.step_3_ul_span2').text(sum);
           }else{
               obj.css('border-color','red');
               obj.val('');
           }

       }
   });
</script>
<?php \frontend\components\JsBlock::end()?>
