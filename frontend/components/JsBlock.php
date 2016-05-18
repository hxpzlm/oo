<?php
/**
 * Created by xiegao .
 * User: Administrator 页面注入js代码
 * Date: 2016/4/11
 * Time: 10:12
 */

namespace frontend\components;
use yii\web\view;
use yii\widgets\Block;

class JsBlock extends Block{
    public $key = null;
    public $pos = View::POS_END;

    public function init()
    {
       parent::init();
    }

    public function run(){
        $block = ob_get_clean();
        if($this->renderInPlace){
            throw new \Exception("not implemented yet!");
        }
        $block = trim($block);
        $jsBlockPattern = '|^<script[^>]*>(?P<block_content>.+?)</script>$|is';
        if(preg_match($jsBlockPattern,$block,$matches)){
            $block =  $matches['block_content'];
        }
        $this->view->registerJs($block, $this->pos,$this->key) ;
    }
}