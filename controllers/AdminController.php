<?php

namespace app\controllers;

use app\components\UserPermissions;
use Yii;
use app\models\User;
use yii\filters\AccessControl;

/**
 * AdminController implements the admin backend overview.
 */
class AdminController extends BaseController
{
    public $layout = 'admin';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => [
                            // allow all admin permissions to view this page
                            UserPermissions::PERMISSION_MANAGE_USERS,
                            UserPermissions::PERMISSION_MANAGE_EXTENSIONS,
                            UserPermissions::PERMISSION_MANAGE_NEWS,
                            UserPermissions::PERMISSION_MANAGE_WIKI,
                            UserPermissions::PERMISSION_MANAGE_COMMENTS,
                            UserPermissions::PERMISSION_MANAGE_FORUM,
                        ],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['discourse'],
                        'roles' => [
                            UserPermissions::PERMISSION_MANAGE_FORUM,
                        ],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        $roles = array_merge(Yii::$app->authManager->getRoles(), Yii::$app->authManager->getPermissions());
        $roleUsers = [];
        foreach($roles as $role) {
            $users = User::findAll(Yii::$app->authManager->getUserIdsByRole($role->name));
            if (!empty($users)) {
                $roleUsers[$role->name] = $users;
            }
        }

        return $this->render('index', [
            'roleUsers' => $roleUsers,
        ]);
    }

    /**
     * Show customization HTML and CSS for discourse header.
     */
    public function actionDiscourse()
    {
        // CSS
        $cssFile = Yii::getAlias('@app/assets/dist/css/header.css');
        if (!file_exists($cssFile)) {
            $css = 'CSS FILE DOES NOT EXIST, check gulp build!';
        } else {
            $css = file_get_contents($cssFile);
        }

        // JS
        $js = file_get_contents(Yii::getAlias('@vendor/bower-asset/bootstrap/js/dropdown.js'));

        // HEADER HTML
        $header = $this->renderPartial('//layouts/partials/_header', ['discourse' => true]);

        $header = preg_replace('~<div class="container">~', '<div class="wrap">', $header);
        $header = preg_replace('~<a\s+href="/~', '<a href="' . Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/', $header);
        $header = preg_replace('~<img\s+src="/~', '<img src="' . Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/', $header);


        return $this->render('discourse', [
            'css' => $css,
            'js' => $js,
            'header' => $header,
        ]);
    }
}
