<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/26
 * Time: 14:13
 */
namespace frontend\components;
use common\models\User;
use frontend\models\Brand;
use frontend\models\Expressway;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\RefuseOrder;
use frontend\models\Customers;
use frontend\models\shop;
use frontend\models\StocksModel;
use frontend\models\Suppliers;
use frontend\models\Purchase;
use frontend\models\Store;
use frontend\models\Unit;
use frontend\models\Category;
use frontend\models\WarehouseModel;
use Yii;
use yii\caching\TagDependency;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Expression;

class Search{

    public static function SearchGoods(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id){
            $goods = Goods::findAll(['store_id'=>$store_id]);
        }else{
            $goods = Goods::find()->all();
        }
        return $goods;
    }

    public static  function SearchBrand(){
        $store_id = Yii::$app->user->identity->store_id;
        $wh['status'] = 1;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }
        $brand = Brand::findAll($wh);
        return $brand;
    }

    public  static function SearchSupplier(){
        $store_id = Yii::$app->user->identity->store_id;
        $wh['status'] = 1;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }
        $supplier = Suppliers::findAll($wh);
        return $supplier;
    }

    public static  function SearchOrder(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
            $order = Order::findAll($wh);
        }else{
            $order= Order::find()->all();
        }
        return $order;
    }
    public static  function SearchUser(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }
        $wh['status'] = 1;
        $user = User::findAll($wh);
        return $user;
    }

    public static  function SearchCustomers(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
            $customers = Customers::findAll($wh);
        }else{
            $customers = Customers::find()->all();
        }
        return $customers;
    }

    public static  function SearchShop(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
            $shop = Shop::findAll($wh);
        }else{
            $shop = Shop::find()->all();
        }
        return $shop;
    }

    public static function SearchStocks($goods_id,$ware){
        if(!empty($goods_id)){
            $wh['goods_id'] = $goods_id;
        }
        if(!empty($ware)){
            $wh['warehouse_id']  = $ware;
        }
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }else{
            $wh='';
        }
        $stocks = StocksModel::find()->select('batch_num,stock_num,unit_name,stocks_id')->where($wh)->all();
        return $stocks;
    }

    public static function SearchStore(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }else{
            $wh='';
        }
        $store = Store::find()->select('name')->where($wh)->all();
        return $store;
    }

    public static function SearchCategory(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }else{
            $wh='';
        }
        $category = Category::find()->select('cat_id,name')->where($wh)->all();
        return $category;
    }

    public static function SearchUnit(){
        $unit = Unit::find()->all();
        return $unit;
    }

    public static function SearchWarehouse(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }else{
            $wh='';
        }
        $warehouse = WarehouseModel::find()->select('name')->where($wh)->all();
        return $warehouse;
    }

    public static function SearchExpressway(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
        }else{
            $wh='';
        }
        $expressway = Expressway::find()->select('name')->where($wh)->all();
        return $expressway;
    }

    public static  function SearchPurchase(){
        $store_id = Yii::$app->user->identity->store_id;
        if($store_id>0){
            $wh['store_id'] = Yii::$app->user->identity->store_id;
            $purchase = Purchase::findAll($wh);
        }else{
            $purchase = Purchase::find()->all();
        }
        return $purchase;
    }
}