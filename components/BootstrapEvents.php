<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\components;


use app\models\Comment;
use app\models\Wiki;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;

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
    }
}