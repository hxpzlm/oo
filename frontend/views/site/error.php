<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = $name;
$this->registerCssFile('@web/statics/css/baocuo.css',['depends'=>['yii\web\YiiAsset']]);
?>
<meta http-equiv="refresh" content="5; url=<?=Yii::$app->request->getReferrer()?>">
<!--主体内容s-->
<div class="baocuo_container">
    <div class="baocuo_main">
        <div class="baocuo_main_top">
             <?= nl2br(Html::encode($message)) ?>
        </div>
        <div class="baocuo_main_bottom">
            <a class="baocuo_btn1" href="<?=Yii::$app->homeUrl?>">
                返回首页
            </a>
            <a class="baocuo_btn2">
                再过
                <span id="baocuo_time">5</span>
                秒返回
            </a>
        </div>
    </div>
</div>
<?php  \frontend\components\JsBlock::begin()?>
<script>
    setTimeout("timer(5)",1000);
    function timer(t)
    {
        var tm = document.getElementById("baocuo_time");
        t-=1;
        tm.innerText=t;
        setTimeout("timer("+t+")",1000);
        
    }
</script>
<?php  \frontend\components\JsBlock::end()?>
