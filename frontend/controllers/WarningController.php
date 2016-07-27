<?php

namespace frontend\controllers;
use frontend\models\Goods;
use frontend\models\StocksModel;
use frontend\models\Warning;
use frontend\models\WarningInfo;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ExpresswayController implements the CRUD actions for Expressway model.
 */
class WarningController extends CommonController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Expressway models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model=WarningInfo::find()->orderBy('id desc');
        $wh['status']=1;
        $s_con = Yii::$app->request->queryParams;
        if(!empty($s_con['goods_name'])) $model->andWhere(['like','goods_name',$s_con['goods_name']]);
        if(!empty($s_con['warehouse_id'])) $model->andWhere(['warehouse_id'=>$s_con['warehouse_id']]);
        if(Yii::$app->user->identity->store_id>0){
            $model->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
            $wh['store_id']=Yii::$app->user->identity->store_id;
        }else{
            if(!empty($s_con['store_id'])){
                $model->andWhere(['store_id'=>$s_con['store_id']]);
                $wh['store_id']=$s_con['store_id'];
            }
        }
        $ware = \frontend\models\WarehouseModel::findAll($wh);
        $countQuery= clone $model;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $data = $model->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        if(Yii::$app->request->get('action')=='export'){
            $final = [['预警信息','预警阀值', '预警时间','短信已发送', '关闭方式']];
            foreach ($countQuery->all() as $v){
                $close="";
                if($v['close_type']>0) $close=($v['close_type']==1)?"系统关闭":"手工关闭";
                $final[] = [
                    $v['info']."\t",$v['warning_num'],date('Y-m-d H:i;s',$v['warning_time']),($v['is_send']==1)?"是":"否",
                    $close."\t"
                ];
            }
            $outFile = 'feed/' . date("YmdHis") . '.xls';
            $ret = \frontend\extend\PHPExcel\Excel::getInstance()->saveSheet($outFile, $final);
            if ($ret) {
                return $this->redirect('/' . $outFile);
            }
        }
        return $this->render('index',[
            'model'=>$data,
            'ware' =>$ware,
            'pagination'=>$pages
        ]);
    }

    public function actionCreate(){
        if(Yii::$app->request->isPost && !Yii::$app->request->isAjax){
            $i=0;
            $s_con=Yii::$app->request->post();
            foreach ($s_con['goods_id'] as $k=>$v){
                if (($model = Warning::findOne(['goods_id'=>$v,'warehouse_id'=>$s_con['warehouse_id']]))!== null){
                    if($s_con['is_warning'][$k]==1){
                        $arr=[
                            'princial_id'=>$s_con['princial_id'][$i],
                            'is_warning'=>$s_con['is_warning'][$k],
                            'warning_num'=>$s_con['warning_num'][$i],
                            'modify_time'=>time(),
                            'modify_user_id'=>Yii::$app->user->id,
                            'modify_user_name'=>Yii::$app->user->identity->real_name,
                        ];
                        $i++;
                    }else{
                        $arr=[
                            'is_warning'=>$s_con['is_warning'][$k],
                            'modify_time'=>time(),
                            'modify_user_id'=>Yii::$app->user->id,
                            'modify_user_name'=>Yii::$app->user->identity->real_name,
                        ];
                    }

                }else{
                    $model = new Warning();
                    if($s_con['is_warning'][$k]==1){
                        $arr=[
                            'warehouse_id'=>$s_con['warehouse_id'],
                            'warehouse_name'=>$s_con['warehouse_name'][$k],
                            'goods_id'=>$s_con['goods_id'][$k],
                            'goods_name'=>$s_con['goods_name'][$k],
                            'spec'      =>$s_con['spec'][$k],
                            'store_id'=>Yii::$app->user->identity->store_id,
                            'store_name'=>Yii::$app->user->identity->store_name,
                            'is_warning'=>$s_con['is_warning'][$k],
                            'warning_num'=>isset($s_con['warning_num'][$i])? $s_con['warning_num'][$i]: 0,
                            'princial_id'=>isset($s_con['princial_id'][$i])?$s_con['princial_id'][$i]:0,
                            'create_time'=>time(),
                            'create_user_id'=>Yii::$app->user->id,
                            'create_user_name'=>Yii::$app->user->identity->real_name,
                        ];
                        $i++;
                    }else{
                        $arr=[
                            'warehouse_id'=>$s_con['warehouse_id'],
                            'warehouse_name'=>$s_con['warehouse_name'][$k],
                            'goods_id'=>$s_con['goods_id'][$k],
                            'goods_name'=>$s_con['goods_name'][$k],
                            'spec'      =>$s_con['spec'][$k],
                            'store_id'=>Yii::$app->user->identity->store_id,
                            'store_name'=>Yii::$app->user->identity->store_name,
                            'is_warning'=>$s_con['is_warning'][$k],
                            'create_time'=>time(),
                            'create_user_id'=>Yii::$app->user->id,
                            'create_user_name'=>Yii::$app->user->identity->real_name,
                        ];
                    }

                }
                $model->setAttributes($arr);
                $model->save();
            }
        }
        if(Yii::$app->request->isAjax){
           $wh['s.warehouse_id'] = Yii::$app->request->post('ware_id');
            $where['status']=1;
            if(Yii::$app->user->identity->store_id>0)
            $where['store_id']=$wh['s.store_id']=Yii::$app->user->identity->store_id;
            $data['goods']=(new Query())->from(StocksModel::tableName().'as s')
                ->select('s.goods_id,s.goods_name,s.spec,s.warehouse_name,w.princial_id,w.is_warning,w.warning_num')
                ->leftJoin(['w'=>Warning::tableName()],'w.goods_id=s.goods_id and w.warehouse_id=s.warehouse_id')
                ->where($wh)->groupBy('s.goods_id,s.warehouse_id')->orderBy('s.goods_id desc')->all();
            $data['user'] = \common\models\User::find()->select('user_id,real_name')->where($where)->orderBy('user_id desc')->asArray()->all();
            Yii::$app->response->format=Response::FORMAT_JSON;
            return $data;
        }
        return $this->render('create');
    }


    public function actionHandle($id)
    {
        $model= $this->findModel($id);
        $model->close_type=2;
        if($model->save()){
            return $this->redirect(['index']);
        }else{
            throw new NotFoundHttpException('预警信息手工关闭失败');
        }

    }


    /**
     * Deletes an existing Expressway model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Expressway model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expressway the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WarningInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('您请求的数据不存在！');
        }
    }
}
