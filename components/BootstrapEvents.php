<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\components;


use app\models\Badge;
use app\models\badges\CivicDutyBadge;
use app\models\badges\CommentatorBadge;
use app\models\badges\CriticBadge;
use app\models\badges\EditorBadge;
use app\models\badges\ExtensionBadge;
use app\models\badges\SupporterBadge;
use app\models\Comment;
use app\models\Wiki;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\User;

class BootstrapEvents implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        // update comment count on a wiki when comments are created, deleted or updated
        Event::on(Comment::class, Comment::EVENT_AFTER_INSERT, [Wiki::class, 'onComment']);
        Event::on(Comment::class, Comment::EVENT_AFTER_UPDATE, [Wiki::class, 'onComment']);
        Event::on(Comment::class, Comment::EVENT_AFTER_DELETE, [Wiki::class, 'onComment']);

        // register events that trigger badge updates
        $badgeEvents = array_merge(
            CivicDutyBadge::updateEvents(),
            CommentatorBadge::updateEvents(),
            CriticBadge::updateEvents(),
            EditorBadge::updateEvents(),
            ExtensionBadge::updateEvents(),
            SupporterBadge::updateEvents()
        );
        foreach($badgeEvents as $event) {
            Event::on($event[0], $event[1], function() {
                Badge::check();
            });
        }
        // add user as candidate for badge update after login
        Event::on(\yii\web\User::class, User::EVENT_AFTER_LOGIN, function($event) {
            Badge::addCandidate($event->sender->id);
        });

    }
}