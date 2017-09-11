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

        $this->stdout("Adding extension admin.\n");
        $this->addExtensionAdmin($auth);

        $this->stdout("Adding wiki admin.\n");
        $this->addWikiAdmin($auth);
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

    /**
     * @param ManagerInterface $auth
     */
    private function addExtensionAdmin($auth)
    {
        $extensionAdmin = $auth->createRole(UserPermissions::ROLE_EXTENSION_ADMIN);
        $extensionAdmin->description = 'Extension admin.';
        $auth->add($extensionAdmin);

        $manageExtensions = $auth->createPermission(UserPermissions::PERMISSION_MANAGE_EXTENSIONS);
        $manageExtensions->description = 'Manage extensions.';
        $auth->add($manageExtensions);

        $auth->addChild($extensionAdmin, $manageExtensions);
    }

    /**
     * @param ManagerInterface $auth
     */
    private function addWikiAdmin($auth)
    {
        $wikiAdmin = $auth->createRole(UserPermissions::ROLE_WIKI_ADMIN);
        $wikiAdmin->description = 'Wiki admin.';
        $auth->add($wikiAdmin);

        $manageWiki = $auth->createPermission(UserPermissions::PERMISSION_MANAGE_WIKI);
        $manageWiki->description = 'Manage wiki.';
        $auth->add($manageWiki);

        $auth->addChild($wikiAdmin, $manageWiki);
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
