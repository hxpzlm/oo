<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
AppAsset::register($this);
use yii\widgets\ActiveForm;
$this->title = '预警设置';
$user = Yii::$app->user->identity;
$this->registerCssFile('@web/statics/css/css_plug/jquery-ui.min.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_plug/jquery-ui.min.js',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/warning.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/warning.js',['depends'=>['yii\web\YiiAsset']]);
$wh['status']=1;
if(Yii::$app->user->identity->store_id>0){
    $wh['store_id']=Yii::$app->user->identity->store_id;
}else{
    $wh['store_id']=Yii::$app->request->get('store_id');
}
$ware = \frontend\models\WarehouseModel::find()->select('warehouse_id,name')->asArray()->where($wh)->all();
?>
<!--主体内容s-->
<div class="warning_mian">
    <span class="jc_infor">基础信息</span>
    <?php $form = ActiveForm::begin([
        'action' => ['create'],
        'method'=>'post'
    ]); ?>
    <div class="lx_select">
        <span>仓库：</span>
        <select name="warehouse_id" id="ware">
            <? foreach ($ware as $k=>$item) {?>
            <option value="<?=$item['warehouse_id']?>" <?=($k==0)?'selected="selected"':""?>><?=$item['name']?></option>
            <?php  }?>
        </select>
    </div>
    <div class="lx_select lx_last">
        <span>预警开关：</span>
        <select class="openshut_btn" w="0">
            <option value="true">开</option>
            <option value="false">关</option>
        </select>
    </div>

    <span class="jc_infor">商品信息</span>
    <div class="lb_box">
        <div class="iDivs"></div>
        <span class="left_box">预警设置:</span>
        <div class="table_box">
            <table class="table_bar" cellpadding="0" cellspacing="0">

            </table>
        </div>
        <div class="clear"></div>
    </div>
    <div class="btn_box">
        <?= Html::submitButton('保存')?>
        <?php ActiveForm::end(); ?>
            <a href="<?=Url::to(['warning/index'])?>" class="ok">返回</a>
    </div>

</div>
<!--主体内容e-->
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(document).ready(function(){
        $.ajax({
            url:'<?=Url::to(['warning/create'])?>',
            type:'post',
            data:{ware_id:<?=$ware[0]['warehouse_id']?>},
            datatype:'json',
            success:function(data){
                var $html="";
                $.each(data.goods,function(index,item){
                    if(item.warning_num==null || item.warning_num==0)  item.warning_num=10;
                    if(item.is_warning==null)  item.is_warning=0;
                    $html+= '<tr><input type="hidden" name="spec[]" value="'+item.spec+'"><input type="hidden" name="warehouse_name[]" value="'+item.warehouse_name+'"><td>'+item.goods_name+'</td><td>'+item.spec+
                        '<input type="hidden" name="goods_name[]" value="'+item.goods_name+'"><input type="hidden" name="goods_id[]" value="'+item.goods_id+'"></td><td><select name="is_warning[]" class="warning_btn" w="'+item.is_warning+'">';
                    if(item.is_warning==1){
                        $html+='<option value="0">不预警</option><option value="1" selected="selected">预警</option>';
                    }else{
                        if(item.is_warning==0){
                            $html+='<option value="1">预警</option><option value="0" selected="selected">不预警</option>';
                        }else{
                            $html+='<option value="1">预警</option><option value="0">不预警</option>';
                        }
                    }
                    $html+='</select></td><td><input type="text" placeholder="预警阀值*" name="warning_num[]"  value="'+item.warning_num+'"/>件'+
                        '</td><td><select  name="princial_id[]">';
                    $.each(data.user,function(i,v){
                        if(v.user_id==item.princial_id){
                            $html+='<option value="'+v.user_id+'" selected="selected">'+v.real_name+'</option>';
                        }else{
                            $html+='<option value="'+v.user_id+'">'+v.real_name+'</option>';
                        }
                    });
                    $html+='</select>' +
                        '</td></tr>';
                });
                $('.table_bar').html($html);
                disabledBtn();
            }
        });

        $('#ware').change(function(){
            $.ajax({
                url:'<?=Url::to(['warning/create'])?>',
                type:'post',
                data:{ware_id:$(this).val()},
                datatype:'json',
                success:function(data){
                    var $html="";
                    $.each(data.goods,function(index,item){
                        if(item.warning_num==null || item.warning_num==0)  item.warning_num=10;
                        if(item.is_warning==null)  item.is_warning=0;
                        $html+= '<tr><input type="hidden" name="spec[]" value="'+item.spec+'"><input type="hidden" name="warehouse_name[]" value="'+item.warehouse_name+'"><td>'+item.goods_name+'' +
                            '</td><td>'+item.spec+
                            '<input type="hidden" name="goods_name[]" value="'+item.goods_name+'"><input type="hidden" name="goods_id[]" value="'+item.goods_id+'"></td><td><select name="is_warning[]" class="warning_btn" w="'+item.is_warning+'">';
                        if(item.is_warning==1){
                            $html+='<option value="0">不预警</option><option value="1" selected="selected">预警</option>';
                        }else{
                            if(item.is_warning==0){
                                $html+='<option value="1">预警</option><option value="0" selected="selected">不预警</option>';
                            }else{
                                $html+='<option value="1">预警</option><option value="0">不预警</option>';
                            }
                        }
                        $html+='</select></td><td><input type="text" placeholder="预警阀值*" name="warning_num[]"  value="'+item.warning_num+'"/>件'+
                            '</td><td><select  name="princial_id[]">';
                        $.each(data.user,function(i,v){
                            if(v.user_id==item.princial_id){
                                $html+='<option value="'+v.user_id+'" selected="selected">'+v.real_name+'</option>';
                            }else{
                                $html+='<option value="'+v.user_id+'">'+v.real_name+'</option>';
                            }

                        });
                        $html+='</select>' +
                            '</td></tr>';
                    });
                    $('.table_bar').html($html);
                    disabledBtn();
                }

            });
        });

        $('.table_bar').on('change','.warning_btn',function () {
            var $this = $(this).parent('td').nextAll('td');
            var Bools = $(this).attr('w');
            if(Bools == '1'){
                $this.find('input').attr('disabled',true);
                $this.find('select').attr('disabled',true);
                $(this).attr('w',0);
            }else{
                $this.find('input').attr('disabled',false);
                $this.find('select').attr('disabled',false);
                $(this).attr('w',1);
            }
        });

        function disabledBtn(){
             for(var i=0;i<$('.warning_btn').length;i++){
             	var $this = $('.warning_btn').eq(i).parent('td').nextAll('td');
             	var Bools = $('.warning_btn').eq(i).attr('w');
             	if(Bools == '1'){
             		$this.find('input').attr('disabled',false);
             		$this.find('select').attr('disabled',false);
             	}else{
             		$this.find('input').attr('disabled',true);
             		$this.find('select').attr('disabled',true);
             	}
             }
        }

    });
</script>

<?php \frontend\components\JsBlock::end()?>

