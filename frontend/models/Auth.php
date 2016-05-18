<?php
/**
 * Created by xiegao.
 * User: Administrator
 * Date: 2016/3/28
 * Time: 16:59
 */
namespace frontend\models;
use Yii;
/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 */
class Auth extends \yii\db\ActiveRecord
{
    /**
     * Auth type
     */
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;
    public $_permissions = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item_child}}';
    }
    /**
     * @inheritdoc
     */
//    public function rules()
//    {
//        return [
//             [['name', 'type'], 'required'],
//            [['type', 'created_at', 'updated_at'], 'integer'],
//            [['description', 'data'], 'string'],
//            [['name', 'rule_name'], 'string', 'max' => 64],
//        ];
//    }
//    /**
//     * @inheritdoc
//     */
//    public function attributeLabels()
//    {
//		 return [
//            'name' => Yii::t('app', '名称'),
//            'type' => Yii::t('app', '角色'),
//            'description' => Yii::t('app', '描述'),
//            'rule_name' => Yii::t('app', '规则名称'),
//        ];
//    }
//    //验证权限是否存在
//    public function validatePermission()
//    {
//        if (!$this->hasErrors()) {
//            $auth = Yii::$app->getAuthManager();
//            if ($this->isNewRecord && $auth->getPermission($this->name)) {
//                $this->addError('name', Yii::t('auth', '这个权限名称已经存在'));
//            }
//            if ($this->isNewRecord && $auth->getRole($this->name)) {
//                $this->addError('name', Yii::t('auth', '这个权限名称已经存在'));
//            }
//        }
//    }
//
//    //创建权限
//    public function createPermission()
//    {
//        if ($this->validate()) {
//            $auth = Yii::$app->getAuthManager();
//            $permission = $auth->createPermission($this->name);
//            $permission->description = $this->description;
//            $auth->add($permission);
//            $admin = $auth->getRole('admin');
//            return $auth->addChild($admin, $permission);
//        }
//        return false;
//    }
//    //编辑/修改权限
//    public function updatePermission($name)
//    {
//        if ($this->validate()) {
//            $auth = Yii::$app->getAuthManager();
//            $permission = $auth->getPermission($name);
//            $permission->description = $this->description;
//            return $auth->update($name, $permission);
//        }
//        return false;
//    }
//    //创建角色
//    public function createRole($permissions)
//    {
//        if ($this->validate()) {
//            $auth = Yii::$app->getAuthManager();
//            $role = $auth->createRole($this->name);
//            $role->description = $this->description;;
//            if ($auth->add($role)) {
//                foreach ($permissions as $permission) {
//                    $obj = $auth->getPermission($permission);
//                    $auth->addChild($role, $obj);
//                }
//                return true;
//            }
//        }
//        return false;
//    }
//
//    //更新角色
//    public function updateRole($name, $permissions)
//    {
//        if ($this->validate()) {
//            $auth = Yii::$app->getAuthManager();
//            $role = $auth->getRole($name);
//            $role->description = $this->description;
//            // save role
//            if ($auth->update($name, $role)) {
//                // remove old permissions
//                $oldPermissions = $auth->getPermissionsByRole($name);
//                foreach($oldPermissions as $permission) {
//                    $auth->removeChild($role, $permission);
//                }
//                // add new permissions
//                foreach ($permissions as $permission) {
//                    $obj = $auth->getPermission($permission);
//                    $auth->addChild($role, $obj);
//                }
//                return true;
//            }
//        }
//        return false;
//    }
//    //角色权限验证
//    public function loadRolePermissions($name) {
//        $models = Yii::$app->authManager->getPermissionsByRole($name);
//        foreach($models as $model) {
//            $this->_permissions[] = $model->name;
//        }
//    }
//    public static function hasUsersByRole($name) {
//        $tablePrefix = Yii::$app->getDb()->tablePrefix;
//        return Auth::find()
//            ->where(['name' => $name])
//            ->leftJoin("{$tablePrefix}auth_assignment", ['item_name' => $name])
//            ->count();
//    }
//    //角色拥有的权限
//    public static function hasRolesByPermission($name) {
//        $tablePrefix = Yii::$app->getDb()->tablePrefix;
//        return Auth::find()
//            ->where(['name' => $name])
//            ->leftJoin("{$tablePrefix}auth_item_child", ['child' => $name])
//            ->count();
//    }
}