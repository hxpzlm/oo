<?php
/**
 * Created by PhpStorm.
 * User: Administrator 商品搜索模糊查询
 * Date: 2016/4/11
 * Time: 14:33
 */
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CommonGoodsSearch extends Goods
{
    public function rules()
    {
        return [
           ['name','string'],

        ];
    }

    public static function getGoods($name){
        $query = Goods::find()->where('store_id='.Yii::$app->user->identity->store_id);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere(['like', 'name', $name]);
        return $dataProvider;
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
}