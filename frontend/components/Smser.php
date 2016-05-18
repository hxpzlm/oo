<?php
/**
 * Created by xiegao.
 * User: Administrator 短信抽象接口
 * Date: 2016/4/7
 * Time: 15:55
 */

namespace daixianceng\smser;
use Yii;
use yii\helpers\FileHelper;
/**
 * 短信发送基类
 *
 * @author Cosmo <daixianceng@gmail.com>
 */
abstract class Smser extends \yii\base\Component
{
    /**
     * 请求地址
     *
     * @var string
     */
    public $url;

    /**
     * 用户名
     *
     * @var string
     */
    public $username;

    /**
     * 密码
     *
     * @var string
     */
    protected $password;

    /**
     * 状态码
     *
     * @var string
     */
    protected $state;

    /**
     * 状态信息
     *
     * @var string
     */
    protected $message;

}