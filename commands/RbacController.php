<?php


namespace app\commands;


use app\components\UserPermissions;
use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\rbac\ManagerInterface;

class RbacController extends Controller
{
    public function actionUp()
    {
        /** @var ManagerInterface $auth */
        $auth = \Yii::$app->authManager;
        
        $this->stdout("Cleaning up.\n");
        $this->cleanup($auth);

        $this->stdout("Adding news admin.\n");
        $this->addNewsAdmin($auth);

        $this->stdout("Adding user admin.\n");
        $this->addUserAdmin($auth);
    }

    /**
     * @param ManagerInterface $auth
     */
    private function cleanup($auth)
    {
        $auth->removeAllPermissions();
        $auth->removeAllRoles();
        $auth->removeAllRules();
    }

    /**
     * @param ManagerInterface $auth
     */
    private function addNewsAdmin($auth)
    {
        $newsAdmin = $auth->createRole(UserPermissions::ROLE_NEWS_ADMIN);
        $newsAdmin->description = 'News admin.';
        $auth->add($newsAdmin);

        $manageNews = $auth->createPermission(UserPermissions::PERMISSION_MANAGE_NEWS);
        $manageNews->description = 'Manage news.';
        $auth->add($manageNews);

        $auth->addChild($newsAdmin, $manageNews);
    }

    /**
     * @param ManagerInterface $auth
     */
    private function addUserAdmin($auth)
    {
        $userAdmin = $auth->createRole(UserPermissions::ROLE_USER_ADMIN);
        $userAdmin->description = 'User admin.';
        $auth->add($userAdmin);

        $manageUsers = $auth->createPermission(UserPermissions::PERMISSION_MANAGE_USERS);
        $manageUsers->description = 'Manage users.';
        $auth->add($manageUsers);

        $auth->addChild($userAdmin, $manageUsers);
    }

    public function actionAssign($username, $role)
    {
        $user = User::find()->where(['username' => $username])->one();
        if (!$user) {
            throw new InvalidParamException("There is no user \"$username\".");
        }

        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole($role);
        if (!$roleObject) {
            throw new InvalidParamException("There is no role \"$role\".");
        }

        $auth->assign($roleObject, $user->id);
    }
}
