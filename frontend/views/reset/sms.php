<?php
/**
 * Created by xiegao.
 * User: Administrator   手机号码验证
 * Date: 2016/4/11
 * Time: 9:13
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use frontend\widgets\Alert;
$this->registerCssFile('@web/statics/css/modifyPassword.css',['depends'=>['yii\web\YiiAsset']]);
$this->title = '验证身份';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modifyPassword_container" xmlns:js="http://www.w3.org/1999/html">
    <div class="modifyPassword_main">
        <div class="modifyPassword_main_top">验证身份</div>
        <div class="modifyPassword01">
            您正在使用手机短信的方式验证身份，请完成以下操作！
        </div>
        <?=Html::beginForm('','post',['id'=>'modifyPassword01']);?>
            <p>
                <label>手机号码：<?=substr_replace($mobile,'****',3,4)?></label>
                <input type="button"  class="modifyPassword01_btn"  id='second' value="获取验证码" style="height:38px;width:120px"/>
            </p>
            <p>
                <label>验证码：</label>
                <input type="text"  name="phoneVerificationCode" placeholder="六位数">
            </p>
            <p class="">
                <?= Html::submitButton('下一步', ['class' => 'modifyPassword_next', 'name' => 'login-button']) ?>
                <a href="<?=Url::to(['reset/index'])?>" href="modifyPassword00.html">上一步</a>
            </p>
        <?=Html::endForm();?>
    </div>
</div>
<?php \frontend\components\JsBlock::begin()?>
<script>
    $(function(){
        $("#second").click(function (){
            sendCode($("#second"));
        });
    })
    //发送验证码
    function sendCode(obj){
        var url = "<?=Url::to(['reset/send'])?>";
        var mobile = <?=$mobile?>;
        //检查手机是否合法
        if(mobile){
            $.ajax({
                url:url,
                data:{'mobile':mobile},
                type:"post",
                success:function(result){
                    if(result.code==2){
                        alert('短信发送成功，验证码10分钟内有效,请注意查看手机短信。如果未收到短信，请在60秒后重试！');
                        settime(obj);//开始倒计时
                    }
                    else{
                        alert('短信发送失败，请和网站客服联系！');
                        return false;
                    }
                }
            });
        }
    }


    //开始倒计时
    function settime(obj){
        var timer = 600;
        var a;
        if(timer > 0){
            a = setInterval(function(){
                timer--;
                if(timer <= 0){
                    clearInterval(a);
                    obj.removeAttr("disabled");
                    obj.val('获取验证码');
                    timer = 5;
                }else{
                    obj.attr("disabled", true);
                    obj.val(timer+ "秒后重发");
                }

            },1000);
        }
    }
</script>
<?php \frontend\components\JsBlock::end()?>
