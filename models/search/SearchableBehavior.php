<?php

namespace app\models\search;

use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * Behavior updates search index when a model changes.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class SearchableBehavior extends Behavior
{
    /**
     * @var string class name of the search model.
     */
    public $searchClass;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function getShowInSearch()
    {
        return true;
    }

    /**
     * @param Event $event
     */
    public function afterInsert($event)
    {
        if ($this->owner->showInSearch) {
            $modelClass = $this->searchClass;
            $modelClass::createRecord($event->sender);
        }
    }

    /**
     * @param Event $event
     */
    public function afterUpdate($event)
    {
        $modelClass = $this->searchClass;
        if ($this->owner->showInSearch) {
            $modelClass::updateRecord($event->sender);
        } else {
            $modelClass::deleteRecord($event->sender);
        }
    }

    /**
     * @param Event $event
     */
    public function afterDelete($event)
    {
        $modelClass = $this->searchClass;
        $modelClass::deleteRecord($event->sender);
    }
}
