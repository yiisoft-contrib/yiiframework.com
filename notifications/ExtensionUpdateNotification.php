<?php

namespace app\notifications;


use app\models\Extension;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class ExtensionUpdateNotification extends BaseNotification
{
    public $extension;

    public $updater;

    public $changes;

    public function init()
    {
        if (!$this->extension instanceof Extension) {
            throw new InvalidConfigException('Extension instance passed to ExtensionUpdateNotification is invalid.');
        }
        parent::init();
    }

    /**
     * @return MessageInterface[]
     */
    public function notify()
    {
        foreach($this->getFollowers($this->extension, $this->updater) as $user) {
            yield $this->buildEmail($user, [
                'extension' => $this->extension,
                'updater' => $this->updater,
                'changes' => $this->changes,
            ]);
        }
    }

    protected function getSubject()
    {
        return "Yii extension updated: {$this->extension->name}";
    }
}
