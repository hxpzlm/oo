<?php
/**
 * Created by xiegao.
 * User: Administrator  库存调剂
 * Date: 2016/4/22
 * Time: 9:02
 */
namespace frontend\controllers;

use frontend\models\Brand;
use frontend\models\Goods;
use frontend\models\StocksModel;
use frontend\models\WarehouseModel;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;
use frontend\models\MoveModel;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class MoveController extends CommonController{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    public function actionIndex(){
        $s_con = Yii::$app->request->queryParams;
        //搜索条件
        $where=array();
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        if(Yii::$app->user->identity->store_id>0){
            $where['m.store_id']=Yii::$app->user->identity->store_id;
        }
        if (!empty($s_con['from_warehouse_id'])) $where['m.from_warehouse_id'] = $s_con['from_warehouse_id']; //仓库
        if (!empty($s_con['to_warehouse_id'])) $where['m.to_warehouse_id'] = $s_con['to_warehouse_id']; //仓库
        $move_time_start = empty(Yii::$app->request->get('move_time_start'))? 0:strtotime(Yii::$app->request->get('move_time_start')); //采购入库开始时间
        $move_time_end = empty(Yii::$app->request->get('move_time_start'))? time():strtotime(Yii::$app->request->get('move_time_end')); //采购退货入库结束时间
        $query = (new \yii\db\Query())->from($tablePrefix.'moving as m')
            ->select('m.from_warehouse_name,m.to_warehouse_name,m.goods_name,m.spec,m.brand_name,m.barode_code,m.number,m.unit_name,m.update_time,m.confirm_time,m.status,m.moving_id')
            ->where($where)->orderBy(['m.status'=>SORT_ASC ]);
        if (!empty($s_con['goods_name'])) $query->andFilterWhere(['like', 'm.goods_name',$s_con['goods_name']]); //商品名称
        if (!empty($s_con['barode_code'])) $query->andFilterWhere(['like', 'm.barode_code',$s_con['barode_code']]); //条形码
        if (!empty($s_con['brand_name'])) $query->andFilterWhere(['like', 'm.brand_name',$s_con['brand_name']]); //品牌
        if($move_time_start<=$move_time_end) {
            if(!empty($move_time_start)) $query->andWhere(['>=','m.update_time',$move_time_start]);
            if(!empty($move_time_end))   $query->andWhere(['<=','m.update_time',$move_time_end]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        //导出表格
        if(Yii::$app->request->get('action')=="export") {
            $final = [['仓库', '商品中英文名称（含规格）', '品牌', '条形码', '调剂数量', '调剂日期', '入库状态', '入库日期']];
            foreach ($query->all() as $feed) {
                // 把需要处理的数据都处理一下
                $final[] = [
                    $feed['from_warehouse_name'].'->'.$feed['to_warehouse_name'], $feed['goods_name']."  ".$feed['spec'], $feed['brand_name'], $feed['barode_code'], $feed['number'].$feed['unit_name'],
                    ($feed['update_time']>0)?date("Y-m-d",$feed['update_time']):"",($feed['status']==0)?"否":"是",($feed['confirm_time']>0)?date("Y-m-d",$feed['confirm_time']):""
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

    public function actionCreate(){
        $model = new MoveModel();
        if($move = Yii::$app->request->post('MoveModel')){
            if($move['from_warehouse_id']==$move['to_warehouse_id']){
                throw new NotFoundHttpException("来源仓和目的仓不能为同一个仓库");
            }
            $model->from_warehouse_id = $move['from_warehouse_id'];
            $model->from_warehouse_name = $this->getWareName($move['from_warehouse_id']);
            $model->to_warehouse_id = $move['to_warehouse_id'];
            $model->to_warehouse_name = $this->getWareName($move['to_warehouse_id']);
            $model->goods_id = $move['goods_id'];
            $model->goods_name = $move['goods_name'];
            $model->brand_id = $move['brand_id'];
            $model->brand_name = $move['brand_name'];
            $model->spec = $move['spec'];
            $model->unit_id = $move['unit_id'];
            $model->unit_name = $move['unit_name'];
            $model->barode_code = $move['barode_code'];
            $model->batch_num = $move['batch_num'];
            $model->number = $move['number'];
            $model->update_time = strtotime($move['update_time']);
            $model->remark = $move['remark'];
            $model->store_id = $move['store_id'];
            $model->store_name = $move['store_name'];
            $model->create_time = $move['create_time'];
            $model->add_user_id = $move['add_user_id'];
            $model->add_user_name = $move['add_user_name'];
            $model->save();
            return $this->redirect(['index']);
        }else{
            if(Yii::$app->user->identity->store_id>0) $where['store_id'] = Yii::$app->user->identity->store_id;
            $ware= WarehouseModel::findAll($where);
            return $this->render('create', [
                'model' => $model,
                'ware'  => $ware,
            ]);
        }
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);
        $where['status'] = 1;
        if(Yii::$app->user->identity->store_id>0) $where['store_id'] = Yii::$app->user->identity->store_id;
        $ware= WarehouseModel::findAll($where);
        //更新采购表
        if ($model->load(Yii::$app->request->post())) {
                $model->update_time = strtotime($model->update_time);
                $model->save();
                return $this->redirect(['index']);
        }else{
            return $this->render('update', [
                'model' => $model,
                'ware'  => $ware,
            ]);
        }
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionHandle($id,$action){
        $model = $this->findModel($id);
        if($action=="comfirm"){
            $bt = Yii::$app->db->beginTransaction();
            $wh['goods_id'] = $model['goods_id'];
            $wh['warehouse_id'] = $model['from_warehouse_id'];
            $wh['batch_num'] = $model['batch_num'];
            if(Yii::$app->user->identity->store_id>0){
                $wh['store_id'] = Yii::$app->user->identity->store_id;
            }
            try{
                if($stocks=StocksModel::findOne($wh)){
                    $stocks->stock_num= $stocks->stock_num-$model['number'];

                    if(!$stocks->save()){
                        throw new \Exception('调剂商品入库失败');
                    }
                }else{
                    throw new \Exception('来源仓库不存在该商品');
                }
                $wh['warehouse_id'] = $model['to_warehouse_id'];
                if($gf= StocksModel::findOne($wh)){
                    $gf->stock_num = $gf['stock_num']+$model['number'];
                    if(!$gf->save()){
                        throw new \Exception('调剂商品进入目标仓库失败');
                    }
                }else{
                    $ss= new StocksModel();
                    $ss->store_id = $model['store_id'];
                    $ss->store_name = $model['store_name'];
                    $ss->warehouse_id = $model['to_warehouse_id'];
                    $ss->warehouse_name = $model['to_warehouse_name'];
                    $ss->goods_id = $model['goods_id'];
                    $ss->goods_name = $model['goods_name'];
                    $ss->brand_id = $model['brand_id'];
                    $ss->brand_name = $model['brand_name'];
                    $ss->barode_code = $model['barode_code'];
                    $goods = Goods::findOne(['goods_id'=>$model['goods_id'],'store_id'=>$model['store_id']]);
                    $ss->cat_id = $goods['cat_id'];
                    $ss->cat_name = $goods['cat_name'];
                    $ss->unit_id = $model['unit_id'];
                    $ss->unit_name = $model['unit_name'];
                    $ss->spec = $model['spec'];
                    $ss->batch_num  = $model['batch_num'];
                    $ss->stock_num  = $model['number'];
                    $ss->purchase_num  = $model['number'];
                    $ss->purchase_time = $stocks->purchase_time;
                    $ss->moving_id  = $id;

                    if(!$ss->save()){
                        throw new \Exception("调剂商品入库失败");
                    }
                }

                $model->confirm_time = time();
                $model->confirm_user_id = Yii::$app->user->id;
                $model->confirm_user_name = Yii::$app->user->identity->real_name;
                $model->status = 1;
                if(!$model->save()){
                    throw new \Exception("确认调剂入库失败");
                }
                $bt->commit();
            }catch(Exception $e){
                $bt->rollBack();
            }
        }
        if($action=="cancle"){
            $bt = Yii::$app->db->beginTransaction();
            $wh['goods_id'] = $model['goods_id'];
            $wh['warehouse_id'] = $model['from_warehouse_id'];
            $wh['batch_num'] = $model['batch_num'];
            if(Yii::$app->user->identity->store_id>0){
                $wh['store_id'] = Yii::$app->user->identity->store_id;
            }
            try{
                if($st=StocksModel::findOne($wh)){
                    $st->stock_num= $st['stock_num']+$model['number'];
                    if(!$st->save()){
                        throw new \Exception('取消调剂商品入库失败');
                    }
                }else{
                    throw new \Exception('该仓库不存在该商品');
                }

                if(StocksModel::findOne(['moving_id'=>$id])){
                    StocksModel::deleteAll(['moving_id'=>$id]);
                }

                    $wh['warehouse_id'] = $model['to_warehouse_id'];
                if($ss = StocksModel::findOne($wh)){
                    if($ss['stock_num']>$model['number']){
                        $ss->stock_num = $ss['stock_num']-$model['number'];
                    }else{
                        $ss->stock_num = "0";
                    }
                    if(!$ss->save()){
                        throw new \Exception("目标仓库取消调剂入库失败");
                    }
                }
                $model->confirm_time = 0;
                $model->confirm_user_id = 0;
                $model->confirm_user_name = "";
                $model->status = 0;
                if(!$model->save()){
                    throw new \Exception("取消调剂入库失败");
                }
                $bt->commit();
            }catch(Exception $e){
                $bt->rollBack();
            }
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MoveModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected  function getWareName($ware_id){
        $data = WarehouseModel::findOne($ware_id);
        return $data['name'];
    }
}
