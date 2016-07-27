<?php
/**
 * Created by Xiegao
 * User: Administrator
 * Date: 2016/3/27
 * Time: 12:03
 */
namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Response;

/**
 * Stocks controller
 */
class StocksController extends CommonController{

    public function actionIndex()
    {
        $s_con = Yii::$app->request->queryParams;
        //搜索条件
        $where=array();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['s.store_id']=Yii::$app->user->identity->store_id;
            
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['s.store_id'] = $sid;
            }
        }
        if (!empty($s_con['warehouse_id'])) $where['s.warehouse_id'] = $s_con['warehouse_id']; //仓库
        $query = (new \yii\db\Query())->from($tablePrefix.'stocks as s')
            ->select('s.goods_id,s.warehouse_id,s.warehouse_name,s.goods_name,s.spec,s.brand_name,s.barode_code,s.cat_name,s.unit_name,sum(s.stock_num) as totle,count(*) as batch_nums')
            ->innerJoin(['w'=>$tablePrefix.'warehouse'],'w.warehouse_id=s.warehouse_id')
            ->where($where)->orderBy(['sum(s.stock_num)'=>SORT_DESC,'s.goods_id'=>SORT_DESC])->groupBy(['s.warehouse_id','s.goods_id']);
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 's.goods_name',html_entity_decode($s_con['goods_name'])]); //商品名称
        if (!empty($s_con['barode_code'])) $query->andFilterWhere(['like', 's.barode_code',$s_con['barode_code']]); //条形码
        if (!empty($s_con['brand_name'])) $query->andFilterWhere(['like', 's.brand_name',html_entity_decode($s_con['brand_name'])]); //品牌
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

      //导出表格
      if(Yii::$app->request->get('action')=="export") {
            $final = [['仓库','商品中英文名称（含规格）', '品牌', '条形码', '商品所属分类', '批次', '库存数量']];
            foreach ($countQuery->all() as $feed) {
                // 把需要处理的数据都处理一下
                $final[] = [
                    $feed['warehouse_name'], $feed['goods_name']."  ".$feed['spec'], $feed['brand_name'], $feed['barode_code']."\t", $feed['cat_name'],
                    $feed['batch_nums'], $feed['totle'].$feed['unit_name'],
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

}
