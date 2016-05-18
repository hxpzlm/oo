<?php
/**
 * Created by xiegao.库存模型
 * User: Administrator
 * Date: 2016/4/13
 * Time: 8:57
 */

namespace frontend\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class StocksModel extends ActiveRecord{


    public static function tableName()
    {
        return '{{%stocks}}';
    }

    /**
     * @inheritdoc
     */
    public static function getGoodsStocks($goods_id,$warehouse_id){
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $db = new \yii\db\Query;
        $db->select('s.batch_num,s.purchase_time,s.purchase_num,s.stock_num,s.unit_name')->from($tablePrefix.'stocks as s')
            ->where(['s.goods_id'=>$goods_id,'s.warehouse_id'=>$warehouse_id])
            ->orderBy(['s.stocks_id'=>SORT_DESC]);
        if(Yii::$app->user->identity->store_id>0){
            $db->andWhere(['s.store_id'=>Yii::$app->user->identity->store_id]);
        }
       return $db->all();
    }

    public static function getSearchData($keyword,$store_id=0){
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $db = new \yii\db\Query;
        $db->select('goods_id,goods_name')->from($tablePrefix.'stocks')->orderBy(['goods_id'=>SORT_DESC]);
        if($store_id>0){
            $db->andWhere(['store_id'=>$store_id]);
        }
        if($keyword!=''){
            $db->andFilterWhere(['like', 'goods_name', $keyword]);
        }
        $dataProvider = $db->all();
        return $dataProvider;
    }
}