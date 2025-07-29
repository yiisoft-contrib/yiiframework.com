<?php

namespace tests\unit\components;

use app\components\GitHubWebhookAction;
use Yii;
use yii\web\Controller;

class GitHubWebhookActionTest extends \Codeception\Test\Unit
{
    /**
     * @var GitHubWebhookAction
     */
    protected $action;

    protected function _before()
    {
        $controller = new Controller('test', Yii::$app);
        $this->action = new GitHubWebhookAction('github-webhook', $controller);
        $this->action->versionsFile = '@tests/_output/test-versions.php';
    }

    public function testGetVersionSeries()
    {
        $method = new \ReflectionMethod($this->action, 'getVersionSeries');
        $method->setAccessible(true);

        // Test Yii 2.0 versions
        $this->assertEquals('2.0', $method->invoke($this->action, '2.0.50', 'yiisoft/yii2'));
        $this->assertEquals('2.0', $method->invoke($this->action, '2.1.0', 'yiisoft/yii2'));
        
        // Test Yii 1.1 versions
        $this->assertEquals('1.1', $method->invoke($this->action, '1.1.30', 'yiisoft/yii'));
        
        // Test invalid versions
        $this->assertNull($method->invoke($this->action, '3.0.0', 'yiisoft/yii2'));
        $this->assertNull($method->invoke($this->action, '2.0.50', 'unknown/repo'));
        $this->assertNull($method->invoke($this->action, '1.0.12', 'yiisoft/yii'));
    }

    public function testCreateVersionsFile()
    {
        $versionsPath = Yii::getAlias($this->action->versionsFile);
        
        // Create a test versions file
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '2.0' => [
            '2.0.49' => 'Aug 30, 2023',
            '2.0.48' => 'May 22, 2023',
        ],
        '1.1' => [
            '1.1.28' => 'February 28, 2023',
            '1.1.27' => 'November 21, 2022',
        ],
    ],
];
PHP;
        
        @mkdir(dirname($versionsPath), 0777, true);
        file_put_contents($versionsPath, $testVersionsContent);
        
        $this->assertFileExists($versionsPath);
        
        // Clean up
        @unlink($versionsPath);
    }
}