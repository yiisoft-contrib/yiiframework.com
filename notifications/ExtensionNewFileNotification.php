<?php

namespace app\notifications;

use app\models\Extension;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class ExtensionNewFileNotification extends BaseNotification
{
    public $extension;

    public $updater;

    public $file;

    public function init()
    {
        if (!$this->extension instanceof Extension) {
            throw new InvalidConfigException('Extension instance passed to ExtensionNewFileNotification is invalid.');
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
                'file' => $this->file,
            ]);
        }
    }

    protected function getSubject()
    {
        return "Yii extension updated: {$this->extension->name}";
    }
}
