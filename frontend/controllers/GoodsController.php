<?php

namespace frontend\controllers;
use frontend\models\Brand;
use frontend\models\StocksModel;
use Yii;
use frontend\models\Goods;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class GoodsController extends CommonController
{

    public function actionIndex()
    {

        $model = new Goods();
        $goods_inf = Yii::$app->request->queryParams;
        $user = Yii::$app->user->identity;
        $where = array();
        if($user->store_id>0){
            $where['store_id']=$user->store_id;
        }else{
            $sid = Yii::$app->request->get('store_id');
            if(!empty($sid)){
                $where['store_id'] = $sid;
            }
        }
        $query = new \Yii\db\Query;
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        $query->select('goods_id,name,spec,brand_name,unit_name,barode_code,weight,volume,shelf_life,cat_id,cat_name,principal_name,sort')->from($tablePrefix.'goods')
            ->where($where)->orderBy(['sort'=>SORT_ASC]);
        if (!empty($goods_inf['name'])) $query->andFilterWhere(['like', 'name',$goods_inf['name']]); //商品名称
        if (!empty($goods_inf['barode_code'])) $query->andFilterWhere(['like', 'barode_code',$goods_inf['barode_code']]); //条形码
        if (!empty($goods_inf['brand_name'])) $query->andFilterWhere(['like', 'brand_name',$goods_inf['brand_name']]); //品牌
        if (!empty($goods_inf['principal_name'])) $query->andFilterWhere(['like', 'principal_name',$goods_inf['principal_name']]); //负责人
        //分类
        $cat_id_get=Yii::$app->request->get('cat_id');
        if($cat_id_get){
            $query->select('goods_id,name,spec,brand_name,unit_name,barode_code,weight,volume,shelf_life,cat_id,cat_name,principal_name,sort')->from($tablePrefix.'goods')
                ->where('cat_id='.$cat_id_get)->orderBy(['sort'=>SORT_ASC,'create_time'=>SORT_DESC]);
        }

        //导出表格
        if(Yii::$app->request->get('action')=="export") {
            $final = [['顺序','商品中英文名称（含规格）', '品牌','单位', '条形码','净重','体积', '保质期','商品所属分类', '负责人']];
            foreach ($query->all() as $feed) {
                // 把需要处理的数据都处理一下
                $final[] = [
                    $feed['sort'],$feed['name']."  ".$feed['spec'], $feed['brand_name'],$feed['unit_name'], $feed['barode_code'],$feed['weight'],
                    $feed['volume'],$feed['shelf_life'],$feed['cat_name'],$feed['principal_name'],
                ];
            }
            // 使用我们写好的saveSheet()方法导出数据
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }
        //确认排序
        if(Yii::$app->request->post('sort')){
            foreach(Yii::$app->request->post('sort') as $k => $v)
            {
                $model = $this->findModel($k);
                $model->sort = $v;

                if($model->save()){
                    $this->redirect(['index']);
                }
            }
        };

        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);
        $countries = $query->orderBy(['sort'=>SORT_ASC,'create_time'=>SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->render('index',[
            'model' => $model,
            'pagination' => $pagination,
            'countries' => $countries,
        ]);
    }

    //创建
    public function actionCreate(){
        $model = new Goods();
        $model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        $tablePrefix = Yii::$app->getDb()->tablePrefix;

            $model->load(Yii::$app->request->post());
        if(empty($model->brand_id) && !empty($model->brand_name)){
            $brand_name = (new Yii\db\Query)->select('brand_id')->from($tablePrefix.'brand')->where('name='."'$model->brand_name.'")->one();
            if(empty($brand_name)){
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script>alert('此品牌名不存在');location.href='';</script>";
                exit;
            }else{
                $model->brand_id=$brand_name['brand_id'];
            }

        }
        if(!empty($model->cat_id)) {
            $category_name = (new Yii\db\Query)->select('name')->from($tablePrefix . 'category')->where('cat_id=' . $model->cat_id)->one();
            $model->cat_name = $category_name['name'];
        }
        if(!empty($model->principal_id)) {
            $user_username = (new Yii\db\Query)->select('real_name')->from($tablePrefix . 'user')->where('user_id=' . $model->principal_id)->one();
            $model->principal_name = $user_username['real_name'];
        }
        $goods = Yii::$app->request->post('Goods');
        $model->unit_name = (!empty($goods['unit_name']) && $goods['unit_name']!='请选择')?$goods['unit_name']:'';
        //print_r(Yii::$app->request->post());die;
        if($model->load(Yii::$app->request->post())&&$model->save()){

            return $this->redirect(['index', 'id' => $model->goods_id]);
        } else {
            $query= new \yii\db\Query;
            $user=Yii::$app->user->identity;
            $store_id=$user->store_id;
            $where=array();$where['status']=1;
            if($store_id>0) $where['store_id']=$store_id;
            //查品牌信息
            $BRAND=$query->select('name,brand_id')->from($tablePrefix.'brand')->where($where)->orderBy(['sort'=>SORT_ASC])->all();
            $brand_row = array();
            if(!empty($BRAND)){
                $brand_row[''] = '请选择';
                foreach($BRAND as $value){
                    $brand_row[$value['brand_id']] = $value['name'];
                }
            }

            //查询分类信息
            $ow=array();$ow['status']=1;
            if($store_id>0) $ow['store_id']=$store_id;
            $ow['parent_id'] = 0;
            $category=$query->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where($ow)->orderBy(['sort'=>SORT_ASC])->all();
            if(!empty($category)){
                $data = array();
                $data[''] = '请选择';
                foreach($category as $v1){
                    $data[$v1['cat_id']] = $v1['name'];
                    $category2=$query->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where('parent_id='.$v1['cat_id'])->orderBy(['sort'=>SORT_ASC])->all();
                    if(!empty($category2)){
                        foreach($category2 as $v2){
                            $data[$v2['cat_id']] = $v2['name'];
                            $category3=$query->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where('parent_id='.$v2['cat_id'])->orderBy(['sort'=>SORT_ASC])->all();
                            if(!empty($category3)){
                                foreach($category3 as $v3){
                                    $data[$v3['cat_id']] = $v3['name'];
                                }
                            }
                        }
                    }

                }
            }

            //查负责人
            $principal=$query->select('real_name,user_id')->from($tablePrefix.'user')->where($where)->orderBy(['sort'=>SORT_ASC])->all();
            $principal_row = array();
            if(!empty($principal)){
                $principal_row[$user->id] = $user->real_name;
                foreach($principal as $value){
                    $principal_row[$value['user_id']] = $value['real_name'];
                }
            }
            return $this->render('create', [
                'model' => $model,
                'brand_row'=>$brand_row,
                'cat_row' => $data,
                'principal_row' => $principal_row,
            ]);
        }
    }

    //修改
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $tablePrefix = Yii::$app->getDb()->tablePrefix;
            $brand_name = Brand::findOne(['brand_id'=>$model->brand_id]);
            $model->brand_name=$brand_name['name'];
            $category_name = (new Yii\db\Query)->select('name')->from($tablePrefix.'category')->where('cat_id='.$model->cat_id)->one();
            $model->cat_name=$category_name['name'];
            $user_username = (new Yii\db\Query)->select('real_name')->from($tablePrefix.'user')->where('user_id='.$model->principal_id)->one();
            $model->principal_name=$user_username['real_name'];

            $goods = Yii::$app->request->post('Goods');
            $model->unit_name = (!empty($goods['unit_name']) && $goods['unit_name']!='请选择')?$goods['unit_name']:'';

            $transaction = Yii::$app->db->beginTransaction();
            //事务回滚
            try {
                if(!$model->save()){
                    throw new \Exception('这里保存失败了,通知事务回滚1');
                }

                yii::$app->db->createCommand()
                    ->update(Yii::$app->getDb()->tablePrefix.'stocks', [
                        'goods_name' => $model['name'],
                    ],'goods_id=:goods_id',[':goods_id'=>$model['goods_id']]);
                //var_dump($aa->execute());exit;
//                if(!$aa->execute()){
//                    throw new \Exception('更新stocks表失败了,通知事务回滚');
//                }
			$transaction->commit(); //提交事务会真正的执行数据库操作
		} catch (Exception $e) {
                //如果操作失败, 数据回滚
                $transaction->rollback();
            }


            return $this->redirect(['index', 'id' => $model->goods_id]);
        } else {
            $query= new \yii\db\Query;
            $user=Yii::$app->user->identity;
            $store_id=$user->store_id;
            $tablePrefix = Yii::$app->getDb()->tablePrefix;
            $where=array();$where['status']=1;
            if($store_id>0) $where['store_id']=$store_id;
            //查品牌信息
            $BRAND=$query->select('name,brand_id')->from($tablePrefix.'brand')->where($where)->orderBy(['sort'=>SORT_ASC])->all();
            $brand_row = array();
            if(!empty($BRAND)){
                $brand_row[] = '请选择';
                foreach($BRAND as $value){
                    $brand_row[$value['brand_id']] = $value['name'];
                }
            }

            //查询分类信息
            $ow=array();$ow['status']=1;
            if($store_id>0) $ow['store_id']=$store_id;
            $ow['parent_id'] = 0;
            $category=$query->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where($ow)->orderBy(['sort'=>SORT_ASC])->all();
            if(!empty($category)){
                $data = array();
                $data[''] = '请选择';
                foreach($category as $v1){
                    $data[$v1['cat_id']] = $v1['name'];
                    $category2=$query->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where('parent_id='.$v1['cat_id'])->orderBy(['sort'=>SORT_ASC])->all();
                    if(!empty($category2)){
                        foreach($category2 as $v2){
                            $data[$v2['cat_id']] = $v2['name'];
                            $category3=$query->select('name,cat_id,parent_id')->from($tablePrefix.'category')->where('parent_id='.$v2['cat_id'])->orderBy(['sort'=>SORT_ASC])->all();
                            if(!empty($category3)){
                                foreach($category3 as $v3){
                                    $data[$v3['cat_id']] = $v3['name'];
                                }
                            }
                        }
                    }

                }
            }

            //查负责人
            $principal=$query->select('real_name,user_id')->from($tablePrefix.'user')->where($where)->orderBy(['sort'=>SORT_ASC])->all();
            $principal_row = array();
            if(!empty($principal)){
                $principal_row[$user->id] = $user->real_name;
                foreach($principal as $value){
                    $principal_row[$value['user_id']] = $value['real_name'];
                }
            }
            return $this->render('update', [
                'model' => $model,
                'brand_row'=>$brand_row,
                'cat_row' => $data,
                'principal_row' => $principal_row,
            ]);
        }
    }

    //删除
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    //查看
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    //
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
