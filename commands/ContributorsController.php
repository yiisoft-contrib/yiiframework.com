<?php
namespace app\commands;

use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\mutex\Mutex;
use yii\di\Instance;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;

/**
 * Generates contributor data: images and a json list
 */
class ContributorsController extends Controller
{
    /**
    * @var Mutex|array|string the mutex object or the application component ID of the mutex.
    * After the controller object is created, if you want to change this property, you should only assign it
    * with a mutex connection object.
    */
    public $mutex = 'yii\mutex\MysqlMutex';

    /**
     * Generates contributor data and avatars
     * @return success or fail
     */
    public function actionGenerate()
    {
        if (!$this->acquireMutex()) {
            $this->stderr("Execution terminated: command is already running.\n", Console::FG_RED);
            return self::EXIT_CODE_ERROR;
        }

        $members = Yii::$app->params['members'];
        $contributors = array();
        $raw_contributors = array();
        $contributorLimit = 1000;

        // getting contributors from github
        try {
            $client = new \Github\Client();
            $token_file = Yii::getAlias('@app/data') . '/github.token';
            if(file_exists($token_file)) {
                $this->stdout("Authenticating with Github token.\n");
                $token = file_get_contents($token_file);
                $client->authenticate($token, null, \Github\Client::AUTH_URL_TOKEN);
            }
            $api = $client->api('repo');
            $paginator  = new \Github\ResultPager($client);
            $parameters = ['yiisoft', 'yii2'];
            $raw_contributors = $paginator->fetch($api, 'contributors', $parameters);
            while($paginator->hasNext() && count($raw_contributors) < $contributorLimit) {
                $raw_contributors = array_merge($raw_contributors, $paginator->fetchNext());
            }

            // remove team members
            $teamGithubs = array_filter(array_map(function($member) { return isset($member['github']) ? $member['github'] : false; }, $members));
            foreach($raw_contributors as $key => $raw_contributor) {
                if (in_array($raw_contributor['login'], $teamGithubs)) {
                    unset($raw_contributors[$key]);
                }
            }
            $raw_contributors = array_slice($raw_contributors, 0, $contributorLimit);
        } catch(\Exception $e) {
            $raw_contributors = false;
        }

        if($raw_contributors) {
            foreach($raw_contributors as $raw_contributor) {
                $contributor = array();
                $contributor['login'] = $raw_contributor['login'];
                $contributor['avatar_url'] = $raw_contributor['avatar_url'];
                $contributor['html_url'] = $raw_contributor['html_url'];
                $contributor['contributions'] = $raw_contributor['contributions'];
                $contributors[] = $contributor;
            }
        }
        // save 'contributors.json' in the data directory
        $data_dir = Yii::getAlias('@app/data');
        file_put_contents($data_dir . DIRECTORY_SEPARATOR . 'contributors.json', json_encode($contributors, JSON_PRETTY_PRINT));

        // Generate avatar thumbnails and store them in data/avatars
        $thumbnail_dir = $data_dir . DIRECTORY_SEPARATOR . 'avatars';
        if(!is_dir($thumbnail_dir)) {
            FileHelper::createDirectory($thumbnail_dir);
        }

        $imagine = new Imagine();
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        $size    = new \Imagine\Image\Box(48, 48);

        foreach($contributors as $contributor) {
            $login = $contributor['login'];

            $thumbFile = $thumbnail_dir . DIRECTORY_SEPARATOR . $login . '.png';
            // test if file exists and is not older than 2 days
            if (is_file($thumbFile) && filemtime($thumbFile) > time() - 86400 * 2) {
                $this->stdout("Using cached $login.png\n");
                continue;
            }

            // Check if the file exists and there are no errors
            $headers = get_headers($contributor['avatar_url'], 1);
            $code = (isset($headers[1])) ? explode(' ', $headers[1]) : explode(' ', $headers[0]);
            $code = next($code);
            if ($code != 404 and $code != 403 and $code != 400 and $code != 500) {
                // the image url seems to be good, save the thumbnail
                $this->stdout("Saving $login.png\n");
                $imagine->open($contributor['avatar_url'])
                    ->thumbnail($size, $mode)
                    ->save($thumbFile);
            } else {
                //TODO: default avatar thumbnail?
                $this->stdout("Avatar $login.png was not found\n");
            }
        }

        if(YII_ENV_DEV) {
            exec('gulp sprites && gulp styles', $output, $ret);
        } else {
            exec('gulp sprites && gulp styles --production', $output, $ret);
        }

        $this->releaseMutex();
        return self::EXIT_CODE_NORMAL;
    }

    /**
    * Acquires current action lock.
    * @return boolean lock acquiring result.
    */
    protected function acquireMutex()
    {
        $this->mutex = Instance::ensure($this->mutex, Mutex::class);
        return $this->mutex->acquire($this->composeMutexName());
    }
    /**
    * Release current action lock.
    * @return boolean lock release result.
    */
    protected function releaseMutex()
    {
        return $this->mutex->release($this->composeMutexName());
    }
    /**
    * Composes the mutex name.
    * @return string mutex name.
    */
    protected function composeMutexName()
    {
        return self::class . '::' . $this->action->getUniqueId();
    }

}
