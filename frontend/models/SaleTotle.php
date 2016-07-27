<?php

namespace frontend\models;

use Yii;


/**
 * This is the model class for table "{{%shop_sale}}".
 *
 * @property integer $shop_id
 * @property string $shop_name
 * @property integer $sale_time
 * @property string $totle_price
 * @property string $sale_nums
 */
class SaleTotle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sale_totle}}';
    }

}