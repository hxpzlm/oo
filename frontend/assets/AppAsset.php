<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
		"statics/css/css_global/global.css",
		"statics/svg/iconfont.css",
		"statics/css/purchaseOrders.css",

    ];
    public $js = [
		"statics/js/js_global/global.js",
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
