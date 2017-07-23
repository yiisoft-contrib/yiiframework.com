<?php

namespace app\notifications;


use app\models\Wiki;
use app\models\WikiRevision;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class WikiUpdateNotification extends BaseNotification
{
    public $wiki;

    public $updater;

    public $changes;

    public function init()
    {
        if (!$this->wiki instanceof Wiki) {
            throw new InvalidConfigException('Wiki instance passed to WikiUpdateNotification is invalid.');
        }
        if (!$this->changes instanceof WikiRevision) {
            throw new InvalidConfigException('Changes instance passed to WikiUpdateNotification is invalid.');
        }
        parent::init();
    }

    /**
     * @return MessageInterface[]
     */
    public function notify()
    {
        foreach($this->getFollowers($this->wiki, $this->updater) as $user) {
            yield $this->buildEmail($user, [
                'wiki' => $this->wiki,
                'updater' => $this->updater,
                'changes' => $this->changes,
            ]);
        }
    }

    protected function getSubject()
    {
        return "Yii tutorial updated: {$this->wiki->title}";
    }
}
