<?php

namespace app\commands;

use app\models\Badge;
use yii\console\Controller;
use Yii;
use yii\db\Query;

/**
 * Handles User Badge calculation
 */
class BadgeController extends Controller
{
    public $cron = false;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['cron']);
    }

    /**
     * Run badge processing for currently queued users.
     */
    public function actionUpdate()
    {
        $ids = (new Query)->select(['user_id'])->distinct()->from('{{%badge_queue}}')->column(Yii::$app->db);
        if (count($ids) > 0) {
            $this->actionProcess($ids);
        }
    }

    /**
     * Process badges for each user.
     */
    public function actionProcess(array $userIds)
    {
        if (empty($userIds)) {
            return;
        }
        $badges = Badge::find()->all();
        foreach($badges as $badge) {
            try {
                // TODO batch per 100
                /** @var $badge Badge */
                $badge->updateBadges($userIds);
            }
            catch(\Exception $e)
            {
                Yii::error($e);
                echo $e;
            }
        }
        $this->cleanup($userIds);
    }

    protected function cleanup($ids)
    {
        Yii::$app->db->createCommand()->delete('{{%badge_queue}}', ['user_id' => $ids])->execute();
    }


    public function actionEnqueue()
    {
        Yii::$app->db->createCommand('INSERT INTO {{%badge_queue}} (user_id) SELECT id FROM {{%user}}')->execute();
    }

    public function actionReset()
    {
        $sql[] = 'UPDATE {{%badges}} SET achieved = 0';
        $sql[] = 'DELETE FROM {{%badge_queue}}';
        $sql[] = 'DELETE FROM {{%user_badges}}';
        $db = Yii::$app->db;
        foreach($sql as $cmd) {
            $db->createCommand($cmd)->execute();
        }
        $this->actionEnqueue();
    }

    public function actionRebuild()
    {
        $this->actionReset();
        $this->actionUpdate();
    }
}
