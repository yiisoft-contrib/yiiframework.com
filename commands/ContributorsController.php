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
    public $mutex = 'yii\mutex\FileMutex';

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

        $github_owner = 'yiisoft';
        $github_repo = 'yii2';
        $curl_url = "https://api.github.com/repos/$github_owner/$github_repo/contributors";

        $raw_output = $this->runCurl($curl_url);

        $output = json_decode($raw_output);

        $contributors = array();

        if(count($output) > 0) {
            foreach($output as $element) {
                $contributor = array();
                $contributor['login'] = $element->login;
                $contributor['avatar_url'] = $element->avatar_url;
                $contributor['contributions'] = $element->contributions;
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
        $size    = new \Imagine\Image\Box(24, 24);

        foreach($contributors as $contributor) {
            $login = $contributor['login'];
            $this->stdout("Saving $login.png\n");
            $imagine->open($contributor['avatar_url'])
                ->thumbnail($size, $mode)
                ->save($thumbnail_dir . DIRECTORY_SEPARATOR . $login . '.png');
        }

        $this->releaseMutex();
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * Calls the Github API using Curl and a Github token.
     * @param  string $curl_url The Github API endpoint to call
     * @return string           The JSON response
     */
    private function runCurl($curl_url) {

        $github_token = '7f69ff487fcfaf21255c53b014c4926f4c231b75';
        $curl_token_auth = 'Authorization: token ' . $github_token;

        $ch = curl_init($curl_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Awesome-Octocat-App', $curl_token_auth));
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
    * Acquires current action lock.
    * @return boolean lock acquiring result.
    */
    protected function acquireMutex()
    {
        $this->mutex = Instance::ensure($this->mutex, Mutex::className());
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
        return $this->className() . '::' . $this->action->getUniqueId();
    }

}
