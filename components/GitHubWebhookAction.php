<?php

namespace app\components;

use yii\base\Action;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use Yii;

/**
 * Action handles GitHub release webhooks to automatically update version information.
 * 
 * This action can be used in any controller as:
 * 
 * public function actions()
 * {
 *     return [
 *         'github-webhook' => [
 *             'class' => 'app\components\GitHubWebhookAction',
 *             'versionsFile' => '@app/config/versions.php',
 *         ]
 *     ];
 * }
 * 
 * GitHub webhook should be configured with:
 * - Payload URL: https://www.yiiframework.com/site/github-webhook
 * - Content type: application/json
 * - Secret: configure in config/params.php as "github-webhook-secret"
 * - Events: Release
 */
class GitHubWebhookAction extends Action
{
    /**
     * @var string Path to the versions.php file
     */
    public $versionsFile = '@app/config/versions.php';

    /**
     * Handles the GitHub webhook request
     * 
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function run()
    {
        if (!Yii::$app->request->isPost) {
            Yii::$app->response->getHeaders()->set('Allow', 'POST');
            throw new MethodNotAllowedHttpException('Only POST requests are allowed');
        }

        $event = Yii::$app->request->headers->get('x-github-event');
        
        // Handle ping event
        if ($event === 'ping') {
            return 'pong';
        }
        
        // Only process release events
        if ($event !== 'release') {
            return 'Event not handled';
        }

        $this->validateSignature();
        $this->processReleaseEvent();
        
        return 'Version updated successfully';
    }

    /**
     * Validates the GitHub webhook signature
     * 
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    protected function validateSignature()
    {
        $secret = Yii::$app->params['github-webhook-secret'] ?? null;
        
        if (empty($secret)) {
            // If no secret is configured, skip validation (not recommended for production)
            return;
        }

        $signature = Yii::$app->request->headers->get('x-hub-signature-256');
        
        if (empty($signature)) {
            throw new ForbiddenHttpException('Missing signature header');
        }

        $payload = file_get_contents('php://input');
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new ForbiddenHttpException('Invalid signature');
        }
    }

    /**
     * Processes the release event and updates version information
     * 
     * @throws BadRequestHttpException
     * @throws Exception
     */
    protected function processReleaseEvent()
    {
        // Parse JSON payload
        Yii::$app->request->parsers = [
            'application/json' => 'yii\web\JsonParser',
        ];
        
        $payload = Yii::$app->request->post();
        
        if (!isset($payload['action']) || !isset($payload['release'])) {
            throw new BadRequestHttpException('Invalid payload format');
        }

        // Only process published releases
        if ($payload['action'] !== 'published') {
            return;
        }

        $release = $payload['release'];
        $repository = $payload['repository'] ?? [];
        
        if (!isset($release['tag_name']) || !isset($repository['full_name'])) {
            throw new BadRequestHttpException('Missing required release information');
        }

        $this->updateVersionsFile($release, $repository);
    }

    /**
     * Updates the versions.php file with new release information
     * 
     * @param array $release Release information from GitHub
     * @param array $repository Repository information from GitHub
     * @throws Exception
     */
    protected function updateVersionsFile($release, $repository)
    {
        $versionsPath = Yii::getAlias($this->versionsFile);
        
        if (!is_file($versionsPath)) {
            throw new Exception('Versions file not found: ' . $versionsPath);
        }

        if (!is_writable($versionsPath)) {
            throw new Exception('Versions file is not writable: ' . $versionsPath);
        }

        $tagName = $release['tag_name'];
        $publishedAt = $release['published_at'];
        $repoName = $repository['full_name'];
        
        // Determine which version series this belongs to
        $versionSeries = $this->getVersionSeries($tagName, $repoName);
        
        if ($versionSeries === null) {
            // Not a supported repository or version format
            return;
        }

        // Format the date
        $date = date('M j, Y', strtotime($publishedAt));
        
        // Read current versions file
        $content = file_get_contents($versionsPath);
        
        // Add the new version to the appropriate series
        $pattern = '/(\'' . preg_quote($versionSeries, '/') . '\'\s*=>\s*\[)(.*?)(\],)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $beforeArray = $matches[1];
            $arrayContent = $matches[2];
            $afterArray = $matches[3];
            
            // Add new version at the beginning of the array
            $newVersionLine = "\n            '$tagName' => '$date',";
            $newArrayContent = $newVersionLine . $arrayContent;
            
            $newContent = str_replace(
                $beforeArray . $arrayContent . $afterArray,
                $beforeArray . $newArrayContent . $afterArray,
                $content
            );
            
            file_put_contents($versionsPath, $newContent);
            
            Yii::info("Updated $versionSeries with version $tagName", __METHOD__);
        }
    }

    /**
     * Determines the version series based on tag name and repository
     * 
     * @param string $tagName
     * @param string $repoName
     * @return string|null
     */
    protected function getVersionSeries($tagName, $repoName)
    {
        // Map repositories to version series
        $repoMapping = [
            'yiisoft/yii2' => '2.0',
            'yiisoft/yii' => '1.1',  // Yii 1.1 releases
        ];

        // Get base version series from repository
        $baseSeries = $repoMapping[$repoName] ?? null;
        
        if ($baseSeries === null) {
            return null;
        }

        // For Yii 2.0, ensure the tag matches 2.x.x pattern
        if ($baseSeries === '2.0' && !preg_match('/^2\.\d+\.\d+/', $tagName)) {
            return null;
        }

        // For Yii 1.1, ensure the tag matches 1.1.x pattern
        if ($baseSeries === '1.1' && !preg_match('/^1\.1\.\d+/', $tagName)) {
            return null;
        }

        return $baseSeries;
    }
}