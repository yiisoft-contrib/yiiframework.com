<?php


namespace app\components;


use app\models\Extension;
use app\models\User;
use app\models\Wiki;
use Yii;

class UserPermissions
{
    /**
     * Authenticated user can create a Wiki page if either:
     *
     * - He has GitHub account attached.
     * - He has email verified.
     *
     * @return bool
     */
    public static function canCreateWikiPage()
    {
        if (Yii::$app->user->isGuest) {
            return false;
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
     *
     * @param Wiki $wikiPage
     * @return bool
     */
    public static function canUpdateWikiPage(Wiki $wikiPage)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if ((int)$wikiPage->creator_id === (int)Yii::$app->user->getId()) {
            return true;
        }

        return true;
    }

    /**
     * Authenticated user can create an Extension if either:
     *
     * - He has GitHub account attached.
     * - He has email verified.
     *
     * @return bool
     */
    public static function canAddOrUpdateExtension()
    {
        if (Yii::$app->user->isGuest) {
            return false;
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
     *
     * @param Extension $extension
     * @return bool
     */
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
}