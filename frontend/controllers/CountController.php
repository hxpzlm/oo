<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/1
 * Time: 15:04
 */
namespace frontend\controllers;
use frontend\models\Goods;
use frontend\models\Shop;
use frontend\models\WarehouseModel;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class CountController extends CommonController{

    public  function actionBuy(){
        $s_con = Yii::$app->request->queryParams;
        $time_start = strtotime(Yii::$app->request->get('time_start')); //销售开始时间
        $time_end = strtotime(Yii::$app->request->get('time_end').' 23:59:59'); //销售结束时间
        $erp = (new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg');

        if(Yii::$app->user->identity->store_id>0){
            $erp->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $erp->andWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if($time_start<=$time_end) {
            if(!empty($time_start)) {
                $erp->andWhere(['>=','purchases_time',$time_start]);
            }
            if(!empty($time_end)) {
                $erp->andWhere(['<=','purchases_time',$time_end]);
            }
        }
        $totle_obj=clone  $erp;
        if(!empty($s_con['goods_name'])){
            $erp->andWhere(['like','goods_name',$s_con['goods_name']]);
        }
        if(!empty($s_con['brand_name'])){
            $erp->andWhere(['like','brand_name',$s_con['brand_name']]);

        }
        if(!empty($s_con['warehouse_id'])){
            $erp->andWhere(['warehouse_id'=>$s_con['warehouse_id']]);
        }
        $totle = $totle_obj->sum('amount');
        $erp->addSelect('goods_name,spec,brand_name,unit_name,sum(amount) as amount,sum(number) as number,(sum(amount)/sum(number)) as avg_price')
            ->addGroupBy(['goods_id'])->addOrderBy(['amount'=>SORT_DESC]);
        $countQuery = clone $erp;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $erp->offset($pages->offset)->limit($pages->limit)->all();
        if(Yii::$app->request->get('action')=="export"){
            $final = [['商品中英文名称','规格', '品牌','单位','采购单价','采购数量','采购金额(元)','采购占比']];
            foreach($countQuery->all() as $v){
                $final[]=[
                  $v['goods_name'],$v['spec'],$v['brand_name'],$v['spec'],round($v['amount']/$v['number'],2),$v['number'],$v['amount'],
                    round(($v['amount']/$totle)*100,2).'%'
                ];
            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }
        return $this->render('buy',[
            'model'=>$dataProvider,
            'totle' => $totle,
            'pagination'=>$pages,
        ]);
    }
    public  function actionSale()
    {
        $s_con = Yii::$app->request->queryParams;
        $time_start = strtotime(Yii::$app->request->get('time_start')); //销售开始时间
        $time_end = strtotime(Yii::$app->request->get('time_end').' 23:59:59'); //销售结束时间
        $erp = (new Query())->from(Yii::$app->getDb()->tablePrefix.'jxc');

        if(Yii::$app->user->identity->store_id>0){
            $erp->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $erp->andWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if($time_start<=$time_end) {
            if(!empty($time_start)) {
                $erp->andWhere(['>=','sale_time',$time_start]);
            }
            if(!empty($time_end)) {
                $erp->andWhere(['<=','sale_time',$time_end]);
            }
        }
        $totle_obj=clone  $erp;
        if(!empty($s_con['goods_name'])){
            $erp->andWhere(['like','goods_name',$s_con['goods_name']]);
        }
        if(!empty($s_con['brand_name'])){
            $erp->andWhere(['like','brand_name',$s_con['brand_name']]);

        }
        if(!empty($s_con['warehouse_id'])){
            $erp->andWhere(['warehouse_id'=>$s_con['warehouse_id']]);
        }
        $totle = $totle_obj->sum('amount');
        $erp->addSelect('goods_name,spec,brand_name,unit_name,sum(amount) as amount,sum(nums) as number,(sum(amount)/sum(nums)) as avg_price')
            ->addGroupBy(['goods_id'])->addOrderBy(['amount'=>SORT_DESC]);
        $countQuery = clone $erp;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $erp->offset($pages->offset)->limit($pages->limit)->all();
        if(Yii::$app->request->get('action')=="export"){
            $final = [['商品中英文名称','规格', '品牌','单位','销售单价','销售数量','销售金额(元)','销售占比']];
            foreach($countQuery->all() as $v){
                $final[]=[
                    $v['goods_name'],$v['spec'],$v['brand_name'],$v['spec'],round($v['amount']/$v['number'],2),$v['number'],$v['amount'],
                    round(($v['amount']/$totle)*100,2).'%'
                ];
            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }
        return $this->render('sale',[
            'model'=>$dataProvider,
            'totle' => $totle,
            'pagination'=>$pages,
        ]);
    }

    public  function actionDelivery()
    {
        $s_con = Yii::$app->request->queryParams;
        $time_start = strtotime(Yii::$app->request->get('time_start')); //销售开始时间
        $time_end = strtotime(Yii::$app->request->get('time_end').' 23:59:59'); //销售结束时间
        $erp = (new Query())->from(Yii::$app->getDb()->tablePrefix.'delivery_totle');
        if(Yii::$app->user->identity->store_id>0){
            $erp->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $erp->andWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if($time_start<=$time_end) {
            if(!empty($time_start)) {
                $erp->andWhere(['>=','sale_time',$time_start]);
            }
            if(!empty($time_end)) {
                $erp->andWhere(['<=','sale_time',$time_end]);
            }
        }
        $totle_obj = clone $erp;
        if(!empty($s_con['delivery_name'])){
            $erp->andWhere(['like','delivery_name',$s_con['delivery_name']]);
        }
        if(!empty($s_con['warehouse_id'])){
            $erp->andWhere(['warehouse_id'=>$s_con['warehouse_id']]);
        }

        $totle=$totle_obj->sum('totle');
        $erp->addSelect('delivery_name,sum(totle) as totle')->addGroupBy('delivery_id')->addOrderBy('totle desc');
        $countQuery = clone $erp;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $erp->offset($pages->offset)->limit($pages->limit)->all();
        if(Yii::$app->request->get('action')=='export'){
            $final = [['物流公司','发单量(笔)','占比']];
            foreach ($countQuery->all() as $v){
                $final[]=[
                    $v['delivery_name'],$v['totle'],round($v['totle']/$totle,2)
                ];
            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }
        return $this->render('delivery',[
            'model'=>$dataProvider,
            'totle' => $totle,
            'pagination'=>$pages,
        ]);
    }

    public  function actionCustomer()
    {
        $s_con = Yii::$app->request->queryParams;
        $time =201606;//date('Ym',time()); //销售结束时间
        $erp = (new Query())->from(Yii::$app->getDb()->tablePrefix.'customer_sale')
        ->select('customers_id,customer_name,real_name,mobile,shop_id,shop_name,sum(nums) as number,sum(real_pay) as amount')
         ->Where(['sale_time'=>$time])->GroupBy(['customers_id','shop_id'])->OrderBy('amount desc');;
        if(Yii::$app->user->identity->store_id>0){
            $erp->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $erp->andWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if(!empty($s_con['user_name'])){
            $erp->andWhere(['like','customer_name',$s_con['user_name']]);
        }
        if(!empty($s_con['shop_id'])){
            $erp->andWhere(['shop_id'=>$s_con['shop_id']]);
        }
        $countQuery= clone $erp;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $erp->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($dataProvider as $k=>$v){
            $numobj =$tobj= (new Query())->from(Yii::$app->getDb()->tablePrefix.'customer_sale');
            $dataProvider[$k]['nums']= $numobj->where(['customers_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','sale_time',$time])->sum('nums');
            $dataProvider[$k]['amounts']= $tobj->where(['customers_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','sale_time',$time])->sum('real_pay');
            $rnumobj =$rtobj= (new Query())->from(Yii::$app->getDb()->tablePrefix.'customer_refuse');
            $dataProvider[$k]['rnums']= $rnumobj->where(['customer_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','refuse_time',$time])->sum('nums');
            $dataProvider[$k]['ramounts']= $rtobj->where(['customer_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','refuse_time',$time])->sum('refuse_amount');

        }
        if(Yii::$app->request->get('action')=='export'){
            $final = [['客户账号','客户姓名','联系电话','销售平台','本月购买次数','本月购买金额(元)','累计购买次数',
                '累计购买金额(元)','累计退货次数','累计退货金额(元)']];
            foreach ($countQuery->all() as $v){
                $numobj =$tobj= (new Query())->from(Yii::$app->getDb()->tablePrefix.'customer_sale');
                $nums= $numobj->where(['customers_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','sale_time',$time])->sum('nums');
                $amounts= $tobj->where(['customers_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','sale_time',$time])->sum('real_pay');
                $rnumobj =$rtobj= (new Query())->from(Yii::$app->getDb()->tablePrefix.'customer_refuse');
                $rnums= $rnumobj->where(['customer_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','refuse_time',$time])->sum('nums');
                $ramounts= $rtobj->where(['customer_id'=>$v['customers_id'],'shop_id'=>$v['shop_id']])->andWhere(['<=','refuse_time',$time])->sum('refuse_amount');
                $final[]=[
                    $v['customer_name'],$v['real_name'],$v['mobile'],$v['shop_name'],$v['number'],$v['amount'],$nums,
                    $amounts,($rnums>0)?$rnums:"0",($ramounts>0)?$ramounts:'0.00'
                ];
            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }
        return $this->render('customer',[
            'model'=>$dataProvider,
            'pagination'=>$pages,
        ]);

    }



    public function actionShop(){
           $s_con = Yii::$app->request->queryParams;
           $sale_time_start = strtotime(Yii::$app->request->get('sale_time_start')); //销售开始时间
           $sale_time_end = strtotime(Yii::$app->request->get('sale_time_end').' 23:59:59'); //销售结束时间
           $shop = (new Query())->from(Yii::$app->getDb()->tablePrefix.'shop_sale');
           $refuse = (new Query())->from(Yii::$app->getDb()->tablePrefix.'shop_refuse')
               ->groupBy(['shop_id'])->orderBy(['shop_id'=>SORT_ASC]);
           if($sale_time_start<=$sale_time_end){
               if(!empty($sale_time_start)) {
                   $shop->andWhere(['>=','sale_time',$sale_time_start]);
                   $refuse->andWhere(['>=','refuse_time',$sale_time_start]);
               }
               if(!empty($sale_time_end)) {
                   $shop->andWhere(['<=','sale_time',$sale_time_end]);
                   $refuse->andWhere(['<=','refuse_time',$sale_time_end]);
               }
           }
           if(!empty($s_con['shop_name'])){
               $shop->andWhere(['like','shop_name',$s_con['shop_name']]);
               $refuse->andWhere(['like','shop_name',$s_con['shop_name']]);
           }
           if(Yii::$app->user->identity->store_id>0){
               $shop->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
               $refuse->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
           }else{
               if(!empty($s_con['store_id'])){
                   $shop->andWhere(['store_id'=>$s_con['store_id']]);
                   $refuse->andWhere(['store_id'=>$s_con['store_id']]);
               }

           }
           $cobj = clone $shop;//销售总金额插叙
           $shop->addGroupBy(['shop_id']);//销售平台分组显示
           $shop->addSelect('shop_id,shop_name,sum(amount) as amount,sum(sale_nums) as sale_nums,count(customer_id) as c_nums')->addOrderBy(['amount'=>SORT_DESC,'shop_id'=>SORT_ASC]);
           $countQuery = clone $shop;//分页
           $robj = clone $refuse;
           $r_count = $robj->sum('r_amount');
           $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
           $dataProvider = $shop->offset($pages->offset)->limit($pages->limit)->all();
           $refuse_data = $refuse->addSelect('shop_id,sum(r_amount) as r_amount,sum(refuse_nums) as refuse_nums')->all();
           foreach ($dataProvider as $k=>$v){
               $dataProvider[$k]['r_amount']="0.00";
               $dataProvider[$k]['refuse_nums']="0";
               foreach ($refuse_data as $val){
                   if($v['shop_id']==$val['shop_id']){
                       $dataProvider[$k]['r_amount']=$val['r_amount'];
                       $dataProvider[$k]['refuse_nums']=$val['refuse_nums'];
                   }
               }
           }
           $count=$cobj->sum('amount');//销售总金额

           if(Yii::$app->request->get('action')=='export'){
               $final = [['销售平台','销售笔数(笔)', '销售金额(元)','客单价(元)','销售占比','退货笔数(笔)','退货金额(元)','退货占比']];
               foreach ($countQuery->all() as $k=>$v){
                   $r_amount="0.00";
                   $refuse_nums="0";
                   foreach ($refuse_data as $val){
                       if($v['shop_id']==$val['shop_id']){
                           $r_amount=$val['r_amount'];
                           $refuse_nums=$val['refuse_nums'];
                       }
                   }
                   $final[]=[
                       $v['shop_name']."\t",$v['sale_nums'],$v['amount'],round($v['amount']/$v['c_nums'],2),($v['amount']>0)?round(($v['amount']/$count)*100,2)."%":'0.00%',
                       $refuse_nums,$r_amount,($r_amount>0) ?round(($r_amount/$r_count)*100,2)."%" : "0.00%"
                   ];
               }
               $outFile = 'feed/'.date("YmdHis") . '.xls';
               $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
               if($ret){
                   return $this->redirect('/'.$outFile);
               }

           }

           return $this->render('shop',[
               'model'=>$dataProvider,
               'count' => $count,
               'r_count' => $r_count,
               'pagination'=>$pages,
           ]);
       }

    public function actionDetails()
    {
        $s_con = Yii::$app->request->queryParams;
        $sale_time_start = strtotime(Yii::$app->request->get('sale_time_start')); //销售开始时间
        $sale_time_end = strtotime(Yii::$app->request->get('sale_time_end').' 23:59:59'); //销售结束时间
        $order = (new Query())->from(Yii::$app->getDb()->tablePrefix.'shop_chuku')
        ->select('shop_id,shop_name,goods_id,goods_name,brand_name,spec,unit_name,sum(nums) as nums,sum(amount) as amount')
        ->groupBy(['shop_id','goods_id'])->orderBy(['amount'=>SORT_DESC]);
        $pur = (new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg')
            ->groupBy(['goods_id'])->orderBy(['goods_id'=>SORT_ASC]);
        if(Yii::$app->user->identity->store_id>0){
            $order->andFilterWhere(['store_id'=>Yii::$app->user->identity->store_id]);
            $pur->andFilterWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $order->andFilterWhere(['store_id'=>$s_con['store_id']]);
                $pur->andFilterWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if(!empty($s_con['shop_name'])){
            $order->andFilterWhere(['like','shop_name',$s_con['shop_name']]);
        }
        if(!empty($s_con['goods_name'])){
            $order->andFilterWhere(['like','goods_name',$s_con['goods_name']]);
            $pur->andFilterWhere(['like','goods_name',$s_con['goods_name']]);
        }
        if(!empty($s_con['brand_name'])){
            $order->andFilterWhere(['like','brand_name',$s_con['brand_name']]);
            $pur->andFilterWhere(['like','brand_name',$s_con['brand_name']]);
        }
        if($sale_time_start<=$sale_time_end) {
            if(!empty($sale_time_start)) {
                $order->andWhere(['>=','sale_time',$sale_time_start]);
            }
            if(!empty($sale_time_end)) {
                $order->andWhere(['<=','sale_time',$sale_time_end]);
                $pur->andWhere(['<=','purchases_time',$sale_time_end]);
            }else{
                $pur->andWhere(['<=','purchases_time',time()]);
            }
        }
        $countQuery = clone $order;
        $p_data = $pur->addSelect('goods_id,(sum(amount)/sum(number)) as ng')->all();
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $order->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($dataProvider as $k=>$v){
                foreach ($p_data as $val){
                    if($v['goods_id']==$val['goods_id']){
                        $dataProvider[$k]['avg']=round($val['ng'],2);
                    }
                }
        }
        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['销售平台','商品中英文名称','规格', '品牌','单位','销售数量','销售成本(元)','销售金额(元)']];
            foreach ($countQuery->all() as $k=>$v){
                foreach ($p_data as $val){
                    if($v['goods_id']==$val['goods_id']){
                        $avg=round($val['ng'],2);
                    }
                }
                $final[]=[
                    $v['shop_name']."\t",$v['goods_name']."\t",$v['brand_name']."\t",$v['spec'],$v['unit_name'],
                    $v['nums'],$avg*$v['nums'],$v['amount']
                ];

            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }

        }
        return $this->render('details',[
            'model'=>$dataProvider,
            'pagination'=>$pages,
        ]);
    }

    public function actionErp(){
        $s_con = Yii::$app->request->queryParams;
        $time_start = strtotime(Yii::$app->request->get('time_start')); //销售开始时间
        $time_end = strtotime(Yii::$app->request->get('time_end').' 23:59:59'); //销售结束时间
        $erp = (new Query())->from(Yii::$app->getDb()->tablePrefix.'jxc')->groupBy(['goods_id']);
        $pobj = (new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg')
            ->select('goods_id,sum(amount) as p_amount,sum(number) as number,(sum(amount)/sum(number)) as avg_price')
            ->groupBy(['goods_id']);
        if(Yii::$app->user->identity->store_id>0){
            $erp->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
            $pobj->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $erp->andWhere(['store_id'=>$s_con['store_id']]);
                $pobj->andWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if(!empty($s_con['goods_name'])){
            $erp->andWhere(['like','goods_name',$s_con['goods_name']]);
            $pobj->andWhere(['like','goods_name',$s_con['goods_name']]);
        }
        if(!empty($s_con['brand_name'])){
            $erp->andWhere(['like','brand_name',$s_con['brand_name']]);
            $pobj->andWhere(['like','brand_name',$s_con['brand_name']]);

        }
        if(!empty($s_con['warehouse_id'])){
            $erp->andWhere(['warehouse_id'=>$s_con['warehouse_id']]);
            $pobj->andWhere(['warehouse_id'=>$s_con['warehouse_id']]);
        }

        if($time_start<=$time_end) {
            if(!empty($time_start)) {
                $erp->andWhere(['>=','sale_time',$time_start]);
            }
            if(!empty($time_end)) {
                $erp->andWhere(['<=','sale_time',$time_end]);
                $pobj->andWhere(['<=','purchases_time',$time_end]);
            }
        }
        $pdata = $pobj->all();
        $erp->addSelect('goods_id,goods_name,spec,brand_name,unit_name,sum(nums) as nums,sum(amount) as amount')
            ->addOrderBy(['amount'=>SORT_DESC]);
        $countQuery = clone $erp;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $erp->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($dataProvider as $k=>$v){
            $dataProvider[$k]['avg_price']=0;
            $dataProvider[$k]['number']=0;
            $dataProvider[$k]['p_amount']=0;
            foreach ($pdata as $val){
                if($v['goods_id']==$val['goods_id']){
                    $dataProvider[$k]['avg_price']=$val['avg_price'];
                    $dataProvider[$k]['number']=$val['number'];
                    $dataProvider[$k]['p_amount']=$val['p_amount'];
                }
            }
        }
        //导出表格
        if(Yii::$app->request->get('action')=='export'){
            $final = [['商品中英文名称','规格', '品牌','单位','采购数量','采购金额(元)','销售数量','销售金额(元)','库存数量','库存金额(元)']];
            foreach ($countQuery->all() as $k=>$v){
                foreach ($pdata as $val){
                    if($v['goods_id']==$val['goods_id']){
                        $final[]=[
                            $v['goods_name']."\t",$v['brand_name']."\t",$v['spec'],$v['unit_name'],($val['number']>0)?$val['number']:0,
                        ($val['p_amount']>0)?$val['p_amount']:"0.00",($v['nums']>0)?$v['nums']:0,empty($v['amount'])?'0.00':$v['amount'],
                            ($val['number']-$v['nums'])."\t",round(($val['number']-$v['nums'])*$val['avg_price'],2)
                        ];
                    }
                }
            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }

        }

        return $this->render('erp',[
            'model'=>$dataProvider,
            'pagination'=>$pages,
        ]);

    }

    public function actionPdetails(){
        $s_con = Yii::$app->request->queryParams;
        $time = empty($s_con['time'])?date('Ym',time()):$s_con['time'];
        $erp = Goods::find();
        $wh['status']=1;
        if(Yii::$app->user->identity->store_id>0){
            $erp->andFilterWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }else{
            if(!empty($s_con['store_id'])){
                $erp->andWhere(['store_id'=>$s_con['store_id']]);
            }
        }
        if(!empty($s_con['goods_name'])){
            $erp->andWhere(['like','name',$s_con['goods_name']]);
        }
        if(!empty($s_con['brand_name'])){
            $erp->andWhere(['like','brand_name',$s_con['brand_name']]);
        }
        $erp->addSelect('goods_id,name,spec,brand_name,unit_name')->orderBy(['goods_id'=>SORT_DESC]);
        $countQuery = clone $erp;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>15]);
        $dataProvider = $erp->asArray()->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($dataProvider as $k=>$v){
            //销售数量统计
            $sales = (new Query())->from(Yii::$app->getDb()->tablePrefix.'detail_sale')->groupBy(['shop_id'])
            ->andWhere(['month'=>$time,'goods_id'=>$v['goods_id']])->addSelect('shop_id,nums,amount')->all();
            $dataProvider[$k]['sales']= !empty($sales)?$sales:"";
            //采购数量详细统计
            $purchase = (new Query())->from(Yii::$app->getDb()->tablePrefix.'detail_purchase')->groupBy(['warehouse_id'])
                ->andWhere(['month'=>$time,'goods_id'=>$v['goods_id']])->addSelect('warehouse_id as ware_id,sum(number) as number,avg(avg_price) as avg_price')->all();
            $dataProvider[$k]['purchase'] = empty($purchase)?"":$purchase;
            //库存数量详细统计

            $stocksobj = (new Query())->from(Yii::$app->getDb()->tablePrefix.'detail_stock')->groupBy(['warehouse_id'])
                ->where(['goods_id'=>$v['goods_id']]);
            $prestock =clone $stocksobj;
            $stocks=$stocksobj->andWhere(['<=','month',$time])->addSelect('warehouse_id as ware_id,sum(stock_num) as stock_num')->all();
            $dataProvider[$k]['stocks'] = empty($stocks)?"":$stocks;
            $stock = $prestock->andWhere(['<','month',$time])->addSelect('warehouse_id as ware_id,sum(stock_num) as stock_num')->all();
            $dataProvider[$k]['stock'] = empty($stock)?"":$stock;
            $avg=(new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg')->select('sum(amount)/sum(number) as avg')->where(['goods_id'=>$v['goods_id']])->andWhere(['<','month',$time])->one();
            $dataProvider[$k]['avg']=$avg['avg'];
            $avg=(new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg')->select('sum(amount)/sum(number) as avg')->where(['goods_id'=>$v['goods_id']])->andWhere(['<=','month',$time])->one();
            $dataProvider[$k]['savg'] = $avg['avg'];
        }
        if(Yii::$app->request->get('action')=='export'){
            $wh['status']=1;
            if(Yii::$app->user->identity->store_id>0){
                $wh['store_id']=Yii::$app->user->identity->store_id;
            }
            $final=[['品牌','商品中英文名称','规格','单位']];
            $shop=Shop::find()->asArray()->select('shop_id,name')->where($wh)->all();
            $ware = WarehouseModel::find()->asArray()->select('warehouse_id,name')->where($wh)->all();
            $arr=[0,1,2,3];
            foreach ($arr as $v){
                if($v==2){
                    foreach ($shop as $v){
                        $final[0][]=$v['name'];
                    }
                    $final[0][]='数量小计';
                    $final[0][]='销售单价';
                    $final[0][]='销售总额';
                }else{
                    foreach ($ware as $v){
                        $final[0][]=$v['name'];
                    }
                    $final[0][]='数量小计';
                    $final[0][]='采购单价';
                    $final[0][]=($v==1)?'采购总额':'库存总额';
                }
            }
            $dataProvider = $countQuery->asArray()->all();
            foreach ($dataProvider as $k=>$v){
                $final[$k+1]=[$v['brand_name'],$v['name'],$v['spec'],$v['unit_name']];
                $ssnums=$samount=$nums=$snums=$pnums=$pamount=0;
                //库存数量详细统计
                $stocksobj = (new Query())->from(Yii::$app->getDb()->tablePrefix.'detail_stock')->groupBy(['warehouse_id'])
                    ->where(['goods_id'=>$v['goods_id']]);
                $prestock =clone $stocksobj;
                $stock = $prestock->andWhere(['<','month',$time])->addSelect('warehouse_id as ware_id,sum(stock_num) as stock_num')->all();
                $avg=(new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg')->select('sum(amount)/sum(number) as avg')->where(['goods_id'=>$v['goods_id']])->andWhere(['<','month',$time])->one();
                foreach ($ware as $val){
                    $num=0;
                    if($stock){
                        foreach ($stock as $value){
                            if(@$value['ware_id']==$val['warehouse_id']){
                                $num = $value['stock_num'];
                                $ssnums=$ssnums+$num;
                            }
                        }
                    }
                    $final[$k+1][]=($num>0)?$num:'0';
                }
                $final[$k+1][]=($ssnums>0)?$ssnums:'0';
                $final[$k+1][]=($avg['avg']>0)?round($avg['avg'],2):'0.00';
                $final[$k+1][]=($ssnums>0)?round($avg['avg']*$ssnums,2):'0.00';
                //采购数量详细统计
                $purchase = (new Query())->from(Yii::$app->getDb()->tablePrefix.'detail_purchase')->groupBy(['warehouse_id'])
                    ->andWhere(['month'=>$time,'goods_id'=>$v['goods_id']])->addSelect('warehouse_id as ware_id,sum(number) as number,avg(avg_price) as avg_price')->all();
                foreach ($ware as $val){
                    $num=0;
                    if($purchase){
                        foreach ($purchase as $value){
                            if(@$value['ware_id']==$val['warehouse_id']){
                                $num = $value['number'];
                                $pnums=$pnums+$num;
                                $pamount = $pamount+$value['avg_price'];
                            }
                        }
                    }
                    $final[$k+1][]=($num>0)?$num:'0';
                }
                $avg = (count($purchase)>0)?round($pamount/count($purchase),2):'0.00';
                $final[$k+1][]=($pnums>0)?$pnums:'0';
                $final[$k+1][]=$avg;
                $final[$k+1][]=($pnums>0)?$avg*$pnums:'0';

                //销售数量统计
                $sales = (new Query())->from(Yii::$app->getDb()->tablePrefix.'detail_sale')->groupBy(['shop_id'])
                    ->andWhere(['month'=>$time,'goods_id'=>$v['goods_id']])->addSelect('shop_id,nums,amount')->all();

                foreach ($shop as $val){
                    $num=0;
                    if($sales){
                        foreach ($sales as $value){
                            if(@$value['shop_id']==$val['shop_id']){
                                $num = $value['nums'];
                                $nums=$nums+$num;
                                $samount = $samount+$value['amount'];

                            }
                        }
                    }
                    $final[$k+1][]=($num>0)?$num:'0';
                }
                $final[$k+1][]=($nums>0)?$nums:'0';
                $final[$k+1][]=($nums>0)?round($samount/$nums,2):'0.00';
                $final[$k+1][]=($samount>0)?$samount:'0.00';
                //结余库存
                $stocks=$stocksobj->andWhere(['<=','month',$time])->addSelect('warehouse_id as ware_id,sum(stock_num) as stock_num')->all();
                $avg=(new Query())->from(Yii::$app->getDb()->tablePrefix.'purchase_avg')->select('sum(amount)/sum(number) as avg')->where(['goods_id'=>$v['goods_id']])->andWhere(['<=','month',$time])->one();
                foreach ($ware as $val){
                    $num=0;
                    if($stocks){
                        foreach ($stocks as $value){
                            if(@$value['ware_id']==$val['warehouse_id']){
                                $num = $value['stock_num'];
                                $snums=$snums+$num;
                            }
                        }
                    }
                    $final[$k+1][]=($num>0)?$num:'0';
                }
                $final[$k+1][]=($snums>0)?$snums:'0';
                $final[$k+1][]=($avg['avg']>0)?round($avg['avg'],2):'0.00';
                $final[$k+1][]=($snums>0)?round($avg['avg']*$snums,2):'0';
            }
            $outFile = 'feed/'.date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if($ret){
                return $this->redirect('/'.$outFile);
            }
        }
        return $this->render('pdetails',[
            'model'=>$dataProvider,
            'pagination'=>$pages,
        ]);



    }
}

?>