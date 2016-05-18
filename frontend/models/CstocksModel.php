<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 13:42
 */
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CstocksModel extends \yii\db\ActiveRecord{

    public static function tableName()
    {
        return '{{%purchase}}';
    }


}