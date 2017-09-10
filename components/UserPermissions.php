<?php


namespace app\components;


use app\models\Extension;
use app\models\Wiki;
use Yii;

class UserPermissions
{
    const ROLE_NEWS_ADMIN = 'news_admin';
    const ROLE_USER_ADMIN = 'user_admin';

    const PERMISSION_MANAGE_NEWS = 'manage_news';
    const PERMISSION_MANAGE_USERS = 'manage_users';

    public static function canCreateWikiPage()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        // TODO: find out and implement criteria to use wiki from the old website

        return true;
    }

    public static function canUpdateWikiPage(Wiki $wikiPage)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        // TODO: find out and implement criteria to use wiki from the old website

        return true;
    }

    public static function canAddOrUpdateExtension()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        // TODO: find out and implement criteria to use extensions from the old website

        return true;
    }

    public static function canUpdateExtension(Extension $extension)
    {
        if (!self::canAddOrUpdateExtension()) {
            return false;
        }

        // can update own extensions only
        if ((int)$extension->owner_id !== (int)Yii::$app->user->id) {
            return false;
        }

        return true;
    }

    public static function canManageNews()
    {
        return Yii::$app->user->can(self::PERMISSION_MANAGE_NEWS);
    }
}