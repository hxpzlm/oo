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
class ShopSale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_sale}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'sale_time', 'sale_nums'], 'integer'],
            [['totle_price'], 'number'],
            [['shop_name'], 'string', 'max' => 32],
        ];
    }
}
