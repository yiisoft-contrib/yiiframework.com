<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 22.07.17
 * Time: 00:30
 */

namespace app\commands;


use app\components\packagist\PackagistApi;
use app\jobs\ExtensionImportJob;
use app\models\ContentShare;
use app\models\Extension;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Console;
use yii\queue\Queue;

/**
 * Cron command executes things that need to be scheduled on a regular basis.
 */
class CronController extends Controller
{
    /**
     * Update extension data from packagist.
     *
     * Cronjob should run hourly.
     * Schedules queue jobs with random delay within the next hour.
     */
    public function actionUpdatePackagist()
    {
        $outdatedExtensions = Extension::find()
            ->andWhere(['from_packagist' => 1])
            ->andWhere('update_time < NOW() - INTERVAL 1 DAY')
            ->all();

        $i = 0;
        foreach($outdatedExtensions as $extension) {
            $extension->updateAttributes(['update_status' => Extension::UPDATE_STATUS_EXPIRED]);

            /** @var $queue Queue */
            $queue = Yii::$app->queue;
            $job = new ExtensionImportJob(['extensionId' => $extension->id]);
            $queue->delay(rand(0, 3600))->push($job);
            ++$i;
        }
        $this->stdout("Scheduled $i packages for update.\n");
    }

    /**
     * Import extensions from Packagist.
     *
     * Cronjob should run daily.
     * Searches Packagist for new yii2 extensions and adds them to the database.
     */
    public function actionImportPackagist()
    {
        $yiiPackages = (new PackagistApi())->listPackageNames('yii2-extension');

        foreach ($yiiPackages as $packageName) {
            $this->stdout('checking ');
            $this->stdout($packageName, Console::BOLD);
            $this->stdout('...');
            $extension = Extension::find()->where(['packagist_url' => Extension::normalizePackagistUrl($packageName)])->one();
            if ($extension !== null) {
                // extension exists, no need to import.
                $this->stdout("exists.\n", Console::FG_GREEN, Console::BOLD);
                continue;
            }
            $this->stdout(" importing... ");

            // TODO implement

            // - solve assign to user (could be based on author section or github user name
            // - do not import low quality packages, implement means to detect low quality / require some download count and similar things

            $this->stdout("done.\n", Console::FG_GREEN, Console::BOLD);
        }

    }

    /**
     * Adds jobs for automatically publication contents (news, wiki, extension) to social networks.
     *
     * NOTE: For new content jobs are created automatically.
     */
    public function actionAddContentShareJobs()
    {
        foreach (ContentShare::$objectTypesData as $objectTypeId => $objectTypeData) {
            $queryIds = (new Query())
                ->from(['o' => $objectTypeData['className']::tableName()])
                ->select('o.id')
                ->andWhere([$objectTypeData['objectStatusPropertyName'] => $objectTypeData['objectStatusPublishedId']])
                ->andWhere([
                    'NOT EXISTS',
                    ContentShare::find()
                        ->alias('cs')
                        ->select(new Expression('1'))
                        ->andWhere(['object_type_id' => $objectTypeId])
                        ->andWhere('cs.object_id = o.id')
                        ->limit(1)
                ]);

            $this->stdout("Start objectTypeId = {$objectTypeId}:\n");
            $current = 0;
            foreach ($queryIds->each(1000) as $arId) {
                $current++;

                $this->stdout("[{$current}] id = {$arId['id']}\n");
                ContentShare::addJobs($objectTypeId, $arId['id']);
            }
            $this->stdout("end;\n\n");
        }
    }
}
