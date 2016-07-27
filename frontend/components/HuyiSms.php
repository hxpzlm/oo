<?php
/**
 * Created by xiegao.
 * User: Administrator 短信发送接口类
 * Date: 2016/4/7
 * Time: 15:47
 */

namespace frontend\components;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\web\session;

class HuyiSms{

    public $url = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit';
    public $username = 'cf_xieryaoye';
    public $password = 'xixieryaoye!!';

    public function send($mobile,$content,$mobile_code=""){

        if(!Yii::$app->session->isActive){
            Yii::$app->session->open();
        }
        $data = [
            'account'=>$this->username,
            'password'=>Md5($this->password),
            'mobile' =>$mobile,
            'content'=>$content,//"。请不要把验证码泄露给其他人。",
          ];
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$this->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        $resultArr = $this->xml($result);
        $status = $resultArr['SubmitResult'];
        if($mobile_code){
            if($status['code']==2){
                Yii::$app->session->set('smsCode',$mobile_code);
            }
        }
        return $status;
    }

    public static function random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    protected function xml($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = self::xml( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return @$arr;
    }

}