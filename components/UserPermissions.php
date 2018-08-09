<?php


namespace app\components;


use app\models\Extension;
use app\models\PasswordResetRequestForm;
use app\models\User;
use app\models\Wiki;
use Yii;

class UserPermissions
{
    const ROLE_NEWS_ADMIN = 'news_admin';
    const ROLE_USER_ADMIN = 'user_admin';
    const ROLE_EXTENSION_ADMIN = 'extension_admin';
    const ROLE_WIKI_ADMIN = 'wiki_admin';
    const ROLE_COMMENT_ADMIN = 'comment_admin';
    const ROLE_FORUM_ADMIN = 'forum_admin';

    const PERMISSION_MANAGE_NEWS = 'manage_news';
    const PERMISSION_MANAGE_USERS = 'manage_users';
    const PERMISSION_MANAGE_EXTENSIONS = 'manage_extensions';
    const PERMISSION_MANAGE_WIKI = 'manage_wiki';
    const PERMISSION_MANAGE_COMMENTS = 'manage_comments';
    const PERMISSION_MANAGE_FORUM = 'manage_forum';

    const MIN_RATING_EDIT_WIKI = 50;

    /**
     * Authenticated user can create a Wiki page if either:
     *
     * - He has GitHub account attached.
     * - He has email verified.
     * - He is Wiki admin.
     *
     * @return bool
     */
    public static function canCreateWikiPage()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (Yii::$app->user->can(self::PERMISSION_MANAGE_WIKI)) {
            return true;
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user->email_verified && $user->getGithub() === null) {
            return false;
        }

        return true;
    }

    /**
     * Authenticated user can edit Wiki page if either:
     *
     * - It's his own page.
     * - He is Wiki admin.'
     * - He has 50 or more rating.
     *
     * @param Wiki $wikiPage
     * @return bool
     */
    public static function canUpdateWikiPage(Wiki $wikiPage)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (Yii::$app->user->can(self::PERMISSION_MANAGE_WIKI)) {
            return true;
        }

        if ((int)$wikiPage->creator_id === (int)Yii::$app->user->getId()) {
            return true;
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        if ($user->rating >= self::MIN_RATING_EDIT_WIKI) {
            return true;
        }

        return false;
    }

    /**
     * Authenticated user can create an Extension if either:
     *
     * - He has GitHub account attached.
     * - He has email verified.
     * - He's extensions admin.
     *
     * @return bool
     */
    public static function canAddOrUpdateExtension()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (self::canManageExtensions()) {
            return true;
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user->email_verified && $user->getGithub() === null) {
            return false;
        }

        return true;
    }

    /**
     * Authenticated user can update an extension if:
     *
     * - He has GitHub account attached or he has email verified.
     * - He's extension owner.
     * - He's extensions admin.
     *
     * @param Extension $extension
     * @return bool
     */
    public static function canUpdateExtension(Extension $extension)
    {
        if (self::canManageExtensions()) {
            return true;
        }

        if (!self::canAddOrUpdateExtension()) {
            return false;
        }

        // can update own extensions only
        if ((int)$extension->owner_id !== (int)Yii::$app->user->id) {
            return false;
        }

        return true;
    }

    /**
     * Authenticated user can manage extensions if he's extension admin.
     */
    public static function canManageExtensions()
    {
        return Yii::$app->user->can(self::PERMISSION_MANAGE_EXTENSIONS);
    }

    /**
     * Authenticated user can manage news if he's news admin.
     *
     * @return bool
     */
    public static function canManageNews()
    {
        return Yii::$app->user->can(self::PERMISSION_MANAGE_NEWS);
    }
}