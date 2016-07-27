<?php
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = '商品分类-修改';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerCssFile('@web/statics/svg/iconfont.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-new.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerCssFile('@web/statics/css/purchaseOrders-ed.css',['depends'=>['yii\web\YiiAsset']]);
$this->registerJsFile('@web/statics/js/js_global/global.js',['depends'=>['yii\web\YiiAsset']]);
?>
<!--内容-->
<div class="container">
	<h4 class="orders-edtade">商品分类信息</h4>
    <?php $form = ActiveForm::begin([
        'id' => 'form-category',
        //'enableAjaxValidation' => true,
        //'enableClientValidation' => true,
    ]); ?>
	<div class="orders-ed clearfix">
		<p>分类名称:</p>
		<?php echo $form->field($model,'name')->textInput(['class'=>'orders-new','autofocus'=>true])->label(false)->hint("<label>*</label>");?>
        <span id="c_name" style="color: red; margin: 0; font-weight: bold;"></span>
	</div>
    <div class="orders-new clearfix">
        <p style="width: 15%">状态:</p>
        <div class="aaa">
            <?php echo $form->field($model,'status')->radioList(['1'=>'正常','0'=>'停用'])?>
        </div>
    </div>
	<div class="orders-ed clearfix">
		<p>顺序:</p>
        <?php echo $form->field($model,'sort')->input(['class'=>'orders-new'])->label(false)->hint("<label>*</label>");?>
	</div>
	<div class="orders-ed clearfix">
		<p class="orders-edt1">备注说明:</p>
        <?php echo $form->field($model,'remark')->textarea(['class'=>'orders-edt2'])?>
	</div>
    <?= Html::activeHiddenInput($model,'parent_id',['value'=>$model->parent_id])?>
    <input type="hidden" name="name_old" value="<?=$model->name?>" />
	<div class="orders-newbut">
		<?=Html::submitButton(Yii::t('app','保存'))?>
		<a href="<?=Url::to(['category/index'])?>">
			<span class="orders-newbut2">返回</span>
		</a>
	</div>
    <?php ActiveForm::end();?>
</div>

<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
        //验证同父级下是否有相同的分类名称
        $("input[name='Category[name]']").blur(function(){
            var name = $("input[name='Category[name]']").val();
            var parent_id = $("input[name='Category[parent_id]']").val();;
            var name_old = $("input[name='name_old']").val();

            if(name!=name_old){
                $.post("<?=Url::to(['category/index'])?>",{action:'e_cname',name:name,parent_id:parent_id},function(result){
                    if(result==0){
                        $("#c_name").text('');
                        $('.orders-edbut').attr('disabled',false);
                        return true;
                    }else{
                        $("#c_name").text('分类名称已存在');
                        $('.orders-edbut').attr('disabled',true);
                        return false;
                    }
                },'json');
            }

        });
    });

</script>
<?php \frontend\components\JsBlock::end()?>
