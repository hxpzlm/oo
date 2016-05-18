<?php
/**
 * Created by xiegao.
 * User: Administrator
 * Date: 2016/4/15
 * Time: 8:36
 */
namespace frontend\controllers;

use frontend\models\Auth;
use frontend\models\SignupForm;
use frontend\models\Store;
use Yii;
use common\models\User;
use yii\base\Model;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\rbac\Item;

class UserController extends CommonController
{
    public function actionIndex()
    {
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $s_con = Yii::$app->request->queryParams;
        $name = empty($s_con['username']) ? "": $s_con['username'];
        $query = User::find()->andFilterWhere(['like','username',$name]);
        if(Yii::$app->user->identity->store_id>0){
            $query->andWhere(['store_id'=>Yii::$app->user->identity->store_id]);
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>20]);
        $dataProvider = $query->orderBy(['sort'=>SORT_ASC,'user_id'=>SORT_DESC])->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        //更新排序
        if(Yii::$app->request->post('sort')){
            foreach(Yii::$app->request->post('sort') as $k => $v)
            {
                $model = $this->findModel($k);
                $model->sort = $v;
                if($model->save()){
                    $this->redirect(['index']);
                }
            }
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    public function actionAdd(){
        $model = new SignupForm();
		$model->load($_POST);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            if(!$model->validate($data['SignupForm'])){
                return null;
            }
            $user = new User();
            foreach($data['SignupForm'] as $k=>$v){
                $user->$k = $v;
            }
            $user->add_user_id= Yii::$app->user->id;
            $user->add_user_name = Yii::$app->user->identity->username;
            $wh['status'] = 1;
            if($data['SignupForm']['store_id']>0){
                $wh['store_id'] = $data['SignupForm']['store_id'];
            }
            $store = Store::findOne($wh);
            $user->store_name = $store['name'];
            $user->setPassword('vtg123');
            $user->generateAuthKey();
            $transaction=Yii::$app->db->beginTransaction();
            try {
                if(!$user->save()){
                    throw new \Exception("添加用户失败");
                }
                $user_id = Yii::$app->db->getLastInsertID();
                $auth = Yii::$app->authManager;
                if($data['SignupForm']['type']==1){
                    $item = new Item();
                    $item->name = "system";
                    if(!$auth->assign($item,$user_id)){
                        throw new \Exception("用户授权失败");
                    }
                }else{
                    $item = new Item();
                    $item->name = $data['SignupForm']['username'];
                    $item->type = 1;
                    $item->description = "创建了".$data['SignupForm']['username']."角色";
                    if(!$auth->add($item)){
                        throw new \Exception("添加用户失败");
                    }

                    foreach($data['child'] as $v){
                        $sql[]= [$data['SignupForm']['username'],$v];
                    }
                    $db= \Yii::$app->db->createCommand();
                    $db->batchInsert(Yii::$app->db->tablePrefix.'auth_item_child',['parent','child'],$sql)->execute();
                    if(!$auth->assign($item,$user_id)){
                        throw new \Exception("用户授权失败");
                    }
                }
                $transaction->commit();
            }catch(Exception $e){
                $transaction->rollBack();
            }
            return $this->redirect(['index']);
        }
        return $this->render('add',[
            'model' => $model,
        ]);
    }

    public function actionUpdate($user_id){
        $user = User::findOne(['user_id'=>$user_id]);
        $username = $user['username'];
		$type = $user['type'];
        $model = new SignupForm();
        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            if(!$model->validate($data['User'])){
                return null;
            }
            foreach($data['User'] as $k=>$v){
                $user->$k = $v;
            }
            $wh['status'] = 1;
            if($data['User']['store_id']!=0){
                $wh['store_id'] = $data['User']['store_id'];
                $store = Store::findOne($wh);
                $user->store_name = $store['name'];
            }
            $transaction=Yii::$app->db->beginTransaction();
            try {
                if(!$user->save()){
                    throw new \Exception("用户编辑失败");
                }
				if($data['child']){
                $auth = Yii::$app->authManager;
                $item = new Item();
                if($data['User']['type']==1){
					if($data['User']['type']!=$type){
						$item->name = $username;
						$auth->remove($item);
						Auth::deleteAll(['parent'=>$username]);
						$auth->revoke($item,$user_id);
						$item->name = "system";
						if(!$auth->assign($item,$user_id)){
                        throw new \Exception("用户授权失败");
						}
					}
                }else{
					if($data['User']['type']!=$type){
						$item->name = "system";
						$auth->revoke($item,$user_id);
					}
                    $item->name = $data['User']['username'];
                    $item->description = "创建了".$data['User']['username']."角色";
                    if($data['User']['type']!=$type){
                        $item->type = 1;
                        if(!$auth->add($item)){
                            throw new \Exception("用户编辑失败");
                        }
                    }else{
                        if(!$auth->update($username,$item)){
                            throw new \Exception("用户编辑失败");
                        }
                    }
                    Auth::deleteAll(['parent'=>$username]);
                    foreach($data['child'] as $v){
                        $sql[]= [$data['User']['username'],$v];
                    }
                    $db= \Yii::$app->db->createCommand();
                    $db->batchInsert(Yii::$app->db->tablePrefix.'auth_item_child',['parent','child'],$sql)->execute();
                    $aa = new Item();
                    $aa->name = $username;
                    $auth->revoke($aa,$user_id);
                    if(!$auth->assign($item,$user_id)){
                        throw new \Exception("用户授权失败");
                    }
				  }
                }
                $transaction->commit();
            }catch(Exception $e){
                $transaction->rollBack();
            }
            return $this->redirect(['index']);
        }
        return $this->render('update',[
            'model' =>$user,
        ]);
    }
    public function actionDelete($user_id){
        $user =Yii::$app->user->identity;
        $where['user_id'] = $user_id;
        if($user->store_id>0){
            $where['store_id'] = $user->store_id;
        }
        $model = User::findOne($where);
        $auth = Yii::$app->authManager;
        $auth->revokeAll($user_id);
        $item = new Item();
        $item->name = $model['username'];
        $auth->removeChildren($item);
        $model->delete();
        return $this->redirect(Url::to(['user/index']));
    }


    public function actionProfile()
    {
        $id = Yii::$app->user->id;
		$user = new User();
        $userInfo = $user->findIdentity($id);
        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            if(!empty($data['oldpwd']))
            {
                if($userInfo->validatePassword($data['oldpwd'])){
                    if(!empty($data['pwd'])){
                          $user->setPassword($data['pwd']);
						  $userInfo->password_hash = $user->password_hash;
                    }
                }
            }
			
            if(!empty($data['real_name'])){
                $userInfo->real_name = $data['real_name'];
            }
            if(!empty($data['mobile'])){
                $userInfo->mobile = $data['mobile'];
            }
            if(!empty($data['email'])){
                $userInfo->email = $data['email'];
            }
            if(isset($data['sex'])){
                $userInfo->sex = $data['sex'];
            }
			    
                $userInfo->save();
                return  $this->goBack();
        }
        return $this->render('profile',[
            'user' => $userInfo,
        ]);
    }

    public function actionView($user_id){
        $model = $this->findModel($user_id);
        return $this->render('view',[
            'model'=>$model,
        ]);
    }

    protected function findModel($user_id)
    {
        if (($model = User::findOne($user_id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}