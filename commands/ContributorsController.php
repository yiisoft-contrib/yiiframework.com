<?php

namespace app\commands;

use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\di\Instance;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\mutex\Mutex;
use yii\web\ErrorHandler;

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
     * @return int
     */
    public function actionGenerate()
    {
        if (!$this->acquireMutex()) {
            $this->stderr("Execution terminated: command is already running.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $members = Yii::$app->params['members'];
        $contributors = [];
        $contributorLimit = 1000;

        // getting contributors from github
        try {
            $client = new \Github\Client();
            $tokenFile = Yii::getAlias('@app/data') . '/github.token';
            if (file_exists($tokenFile)) {
                $this->stdout("Authenticating with Github token.\n");
                $token = trim(file_get_contents($tokenFile));
                $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
            }
            
            // Get all repositories from yiisoft organization
            $orgApi = $client->api('organization');
            $orgPaginator = new \Github\ResultPager($client);
            $this->stdout("Fetching all repositories from yiisoft organization...\n");
            $repositories = $orgPaginator->fetch($orgApi, 'repositories', ['yiisoft']);
            
            while ($orgPaginator->hasNext()) {
                $repositories = array_merge($repositories, $orgPaginator->fetchNext());
            }
            
            $this->stdout("Found " . count($repositories) . " repositories in yiisoft organization.\n");
            
            // Aggregate contributors from all repositories
            $allContributors = [];
            $api = $client->api('repo');
            
            foreach ($repositories as $repo) {
                $repoName = $repo['name'];
                $this->stdout("Fetching contributors from yiisoft/{$repoName}...\n");
                
                try {
                    $paginator = new \Github\ResultPager($client);
                    $parameters = ['yiisoft', $repoName];
                    $repoContributors = $paginator->fetch($api, 'contributors', $parameters);
                    
                    while ($paginator->hasNext() && count($allContributors) < $contributorLimit) {
                        $repoContributors = array_merge($repoContributors, $paginator->fetchNext());
                    }
                    
                    // Merge contributors, summing contributions for duplicates
                    foreach ($repoContributors as $contributor) {
                        $login = $contributor['login'];
                        if (isset($allContributors[$login])) {
                            $allContributors[$login]['contributions'] += $contributor['contributions'];
                        } else {
                            $allContributors[$login] = $contributor;
                        }
                    }
                    
                    if (count($allContributors) >= $contributorLimit) {
                        $this->stdout("Reached contributor limit of {$contributorLimit}.\n");
                        break;
                    }
                } catch (\Exception $e) {
                    $this->stdout("Warning: Could not fetch contributors from {$repoName}: " . $e->getMessage() . "\n");
                    continue;
                }
            }
            
            // Convert back to indexed array and sort by contributions
            $rawContributors = array_values($allContributors);
            usort($rawContributors, function($a, $b) {
                return $b['contributions'] - $a['contributions'];
            });

            // remove team members
            $teamGithubs = array_filter(array_map(function ($member) {
                return $member['github'] ?? false;
            }, $members));
            foreach ($rawContributors as $key => $rawContributor) {
                if (in_array($rawContributor['login'], $teamGithubs)) {
                    unset($rawContributors[$key]);
                }
            }
            $rawContributors = array_slice($rawContributors, 0, $contributorLimit);
        } catch (\Exception $e) {
            $errorString = ErrorHandler::convertExceptionToVerboseString($e);
            $this->stdout("Exception occured during fetching contributors: $errorString\n");
            $rawContributors = false;
        }

        if ($rawContributors) {
            foreach ($rawContributors as $rawContributor) {
                $contributor = array();
                $contributor['login'] = $rawContributor['login'];
                $contributor['avatar_url'] = $rawContributor['avatar_url'];
                $contributor['html_url'] = $rawContributor['html_url'];
                $contributor['contributions'] = $rawContributor['contributions'];
                $contributors[] = $contributor;
            }
        }
        // save 'contributors.json' in the data directory
        $data_dir = Yii::getAlias('@app/data');
        file_put_contents($data_dir . DIRECTORY_SEPARATOR . 'contributors.json', json_encode($contributors, JSON_PRETTY_PRINT));

        // Generate avatar thumbnails and store them in data/avatars
        $thumbnail_dir = $data_dir . DIRECTORY_SEPARATOR . 'avatars';
        if (!is_dir($thumbnail_dir)) {
            FileHelper::createDirectory($thumbnail_dir);
        }

        $imagine = new Imagine();
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        $size = new \Imagine\Image\Box(48, 48);

        foreach ($contributors as $contributor) {
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

        if (YII_ENV_DEV) {
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
