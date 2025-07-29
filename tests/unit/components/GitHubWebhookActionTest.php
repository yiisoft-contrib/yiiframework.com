<?php

namespace tests\unit\components;

use app\components\GitHubWebhookAction;
use Yii;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;

class GitHubWebhookActionTest extends \Codeception\Test\Unit
{
    /**
     * @var GitHubWebhookAction
     */
    protected $action;

    /**
     * @var string
     */
    protected $testVersionsPath;

    protected function _before()
    {
        $controller = new Controller('test', Yii::$app);
        $this->action = new GitHubWebhookAction('github-webhook', $controller);
        $this->action->versionsFile = '@tests/_output/test-versions.php';
        $this->testVersionsPath = Yii::getAlias($this->action->versionsFile);
        
        // Ensure output directory exists
        @mkdir(dirname($this->testVersionsPath), 0777, true);
    }

    protected function _after()
    {
        // Clean up test files
        @unlink($this->testVersionsPath);
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
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        $this->assertFileExists($this->testVersionsPath);
    }

    public function testValidateSignatureWithValidSecret()
    {
        // Mock request with valid signature
        $payload = '{"test": "data"}';
        $secret = 'test-secret';
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        
        // Set up Yii app params
        Yii::$app->params['github-webhook-secret'] = $secret;
        
        // Mock request headers
        Yii::$app->request->headers->set('x-hub-signature-256', $signature);
        
        $method = new \ReflectionMethod($this->action, 'validateSignature');
        $method->setAccessible(true);
        
        // Should not throw exception with valid signature
        $method->invoke($this->action, $payload);
        $this->assertTrue(true); // If we get here, validation passed
    }

    public function testValidateSignatureWithInvalidSecret()
    {
        $payload = '{"test": "data"}';
        $secret = 'test-secret';
        $wrongSignature = 'sha256=' . hash_hmac('sha256', $payload, 'wrong-secret');
        
        Yii::$app->params['github-webhook-secret'] = $secret;
        Yii::$app->request->headers->set('x-hub-signature-256', $wrongSignature);
        
        $method = new \ReflectionMethod($this->action, 'validateSignature');
        $method->setAccessible(true);
        
        $this->expectException(ForbiddenHttpException::class);
        $this->expectExceptionMessage('Invalid signature');
        $method->invoke($this->action, $payload);
    }

    public function testValidateSignatureWithMissingHeader()
    {
        Yii::$app->params['github-webhook-secret'] = 'test-secret';
        Yii::$app->request->headers->remove('x-hub-signature-256');
        
        $method = new \ReflectionMethod($this->action, 'validateSignature');
        $method->setAccessible(true);
        
        $this->expectException(ForbiddenHttpException::class);
        $this->expectExceptionMessage('Missing signature header');
        $method->invoke($this->action);
    }

    public function testValidateSignatureWithNoSecret()
    {
        // When no secret is configured, validation should be skipped
        unset(Yii::$app->params['github-webhook-secret']);
        
        $method = new \ReflectionMethod($this->action, 'validateSignature');
        $method->setAccessible(true);
        
        // Should not throw exception when no secret is configured
        $method->invoke($this->action);
        $this->assertTrue(true);
    }

    public function testRunWithNonPostRequest()
    {
        // Mock non-POST request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Yii::$app->request->setIsPost(false);
        
        $this->expectException(MethodNotAllowedHttpException::class);
        $this->expectExceptionMessage('Only POST requests are allowed');
        $this->action->run();
    }

    public function testRunWithPingEvent()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Yii::$app->request->setIsPost(true);
        Yii::$app->request->headers->set('x-github-event', 'ping');
        
        $result = $this->action->run();
        $this->assertEquals('pong', $result);
    }

    public function testRunWithNonReleaseEvent()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Yii::$app->request->setIsPost(true);
        Yii::$app->request->headers->set('x-github-event', 'push');
        
        $result = $this->action->run();
        $this->assertEquals('Event not handled', $result);
    }

    public function testUpdateVersionsFileWithNewYii2Release()
    {
        // Create test versions file
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
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        
        // Mock release data
        $release = [
            'tag_name' => '2.0.50',
            'published_at' => '2024-01-15T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii2'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        $method->invoke($this->action, $release, $repository);
        
        // Verify the file was updated
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertStringContains("'2.0.50' => 'Jan 15, 2024',", $updatedContent);
        
        // Verify the new version was added at the top
        $this->assertStringContains("'2.0.50' => 'Jan 15, 2024',\n            '2.0.49' => 'Aug 30, 2023',", $updatedContent);
    }

    public function testUpdateVersionsFileWithNewYii11Release()
    {
        // Create test versions file
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
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        
        // Mock release data
        $release = [
            'tag_name' => '1.1.29',
            'published_at' => '2024-02-20T14:15:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        $method->invoke($this->action, $release, $repository);
        
        // Verify the file was updated
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertStringContains("'1.1.29' => 'Feb 20, 2024',", $updatedContent);
        
        // Verify the new version was added at the top of 1.1 series
        $this->assertStringContains("'1.1.29' => 'Feb 20, 2024',\n            '1.1.28' => 'February 28, 2023',", $updatedContent);
    }

    public function testUpdateVersionsFileWithDuplicateVersion()
    {
        // Create test versions file with existing version
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '2.0' => [
            '2.0.50' => 'Jan 15, 2024',
            '2.0.49' => 'Aug 30, 2023',
        ],
        '1.1' => [
            '1.1.28' => 'February 28, 2023',
        ],
    ],
];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        $originalContent = file_get_contents($this->testVersionsPath);
        
        // Try to add duplicate version
        $release = [
            'tag_name' => '2.0.50',
            'published_at' => '2024-01-15T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii2'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        $method->invoke($this->action, $release, $repository);
        
        // Verify the file was not changed
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertEquals($originalContent, $updatedContent);
    }

    public function testUpdateVersionsFileWithUnsupportedRepository()
    {
        // Create test versions file
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '2.0' => [
            '2.0.49' => 'Aug 30, 2023',
        ],
        '1.1' => [
            '1.1.28' => 'February 28, 2023',
        ],
    ],
];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        $originalContent = file_get_contents($this->testVersionsPath);
        
        // Try to add version from unsupported repository
        $release = [
            'tag_name' => '3.0.0',
            'published_at' => '2024-01-15T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'some/other-repo'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        $method->invoke($this->action, $release, $repository);
        
        // Verify the file was not changed
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertEquals($originalContent, $updatedContent);
    }

    public function testUpdateVersionsFileWithInvalidVersionFormat()
    {
        // Create test versions file
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '2.0' => [
            '2.0.49' => 'Aug 30, 2023',
        ],
        '1.1' => [
            '1.1.28' => 'February 28, 2023',
        ],
    ],
];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        $originalContent = file_get_contents($this->testVersionsPath);
        
        // Try to add invalid version format
        $release = [
            'tag_name' => '3.0.0',  // Invalid for yii2 repo
            'published_at' => '2024-01-15T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii2'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        $method->invoke($this->action, $release, $repository);
        
        // Verify the file was not changed
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertEquals($originalContent, $updatedContent);
    }

    public function testProcessReleaseEventWithValidPayload()
    {
        // Create test versions file
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '2.0' => [
            '2.0.49' => 'Aug 30, 2023',
        ],
        '1.1' => [
            '1.1.28' => 'February 28, 2023',
        ],
    ],
];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        
        // Mock valid release payload
        $payload = [
            'action' => 'published',
            'release' => [
                'tag_name' => '2.0.51',
                'published_at' => '2024-03-01T12:00:00Z'
            ],
            'repository' => [
                'full_name' => 'yiisoft/yii2'
            ]
        ];
        
        // Mock request post data
        $_POST = $payload;
        Yii::$app->request->setBodyParams($payload);
        
        $method = new \ReflectionMethod($this->action, 'processReleaseEvent');
        $method->setAccessible(true);
        $method->invoke($this->action);
        
        // Verify the file was updated
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertStringContains("'2.0.51' => 'Mar 1, 2024',", $updatedContent);
    }

    public function testProcessReleaseEventWithNonPublishedAction()
    {
        // Create test versions file
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '2.0' => [
            '2.0.49' => 'Aug 30, 2023',
        ],
    ],
];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        $originalContent = file_get_contents($this->testVersionsPath);
        
        // Mock release payload with non-published action
        $payload = [
            'action' => 'created',  // Not 'published'
            'release' => [
                'tag_name' => '2.0.51',
                'published_at' => '2024-03-01T12:00:00Z'
            ],
            'repository' => [
                'full_name' => 'yiisoft/yii2'
            ]
        ];
        
        $_POST = $payload;
        Yii::$app->request->setBodyParams($payload);
        
        $method = new \ReflectionMethod($this->action, 'processReleaseEvent');
        $method->setAccessible(true);
        $method->invoke($this->action);
        
        // Verify the file was not changed
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertEquals($originalContent, $updatedContent);
    }

    public function testProcessReleaseEventWithMissingPayloadData()
    {
        // Mock invalid payload missing required fields
        $payload = [
            'action' => 'published'
            // Missing 'release' and 'repository'
        ];
        
        $_POST = $payload;
        Yii::$app->request->setBodyParams($payload);
        
        $method = new \ReflectionMethod($this->action, 'processReleaseEvent');
        $method->setAccessible(true);
        
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Invalid payload format');
        $method->invoke($this->action);
    }

    public function testCompleteWorkflowWithValidReleaseWebhook()
    {
        // Create test versions file
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
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        
        // Mock complete webhook payload
        $payload = json_encode([
            'action' => 'published',
            'release' => [
                'tag_name' => '2.0.54',
                'published_at' => '2024-07-29T10:30:00Z'
            ],
            'repository' => [
                'full_name' => 'yiisoft/yii2'
            ]
        ]);
        
        $secret = 'test-webhook-secret';
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        
        // Set up environment for full workflow test
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Yii::$app->params['github-webhook-secret'] = $secret;
        Yii::$app->request->setIsPost(true);
        Yii::$app->request->headers->set('x-github-event', 'release');
        Yii::$app->request->headers->set('x-hub-signature-256', $signature);
        Yii::$app->request->setBodyParams(json_decode($payload, true));
        
        // Override validateSignature to use our test payload
        $reflection = new \ReflectionClass($this->action);
        $validateMethod = $reflection->getMethod('validateSignature');
        $validateMethod->setAccessible(true);
        
        // Create a mock action that overrides validateSignature
        $mockAction = new class('github-webhook', new Controller('test', Yii::$app)) extends GitHubWebhookAction {
            public $testPayload;
            
            protected function validateSignature($payload = null)
            {
                return parent::validateSignature($this->testPayload);
            }
        };
        
        $mockAction->versionsFile = $this->action->versionsFile;
        $mockAction->testPayload = $payload;
        
        // Run the complete workflow
        $result = $mockAction->run();
        
        // Verify result
        $this->assertEquals('Version updated successfully', $result);
        
        // Verify the file was updated correctly
        $updatedContent = file_get_contents($this->testVersionsPath);
        $this->assertStringContains("'2.0.54' => 'Jul 29, 2024',", $updatedContent);
        
        // Verify the new version was added at the top
        $this->assertStringContains("'2.0.54' => 'Jul 29, 2024',\n            '2.0.49' => 'Aug 30, 2023',", $updatedContent);
    }

    public function testFileNotWritableError()
    {
        // Create test versions file and make it read-only
        $testVersionsContent = <<<'PHP'
<?php
return ['minor-versions' => ['2.0' => []]];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        chmod($this->testVersionsPath, 0444); // Read-only
        
        $release = [
            'tag_name' => '2.0.55',
            'published_at' => '2024-07-29T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii2'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        
        $this->expectException(\yii\base\Exception::class);
        $this->expectExceptionMessage('Versions file is not writable');
        $method->invoke($this->action, $release, $repository);
        
        // Restore permissions for cleanup
        chmod($this->testVersionsPath, 0644);
    }

    public function testFileNotFoundError()
    {
        // Don't create the versions file
        $release = [
            'tag_name' => '2.0.55',
            'published_at' => '2024-07-29T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii2'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        
        $this->expectException(\yii\base\Exception::class);
        $this->expectExceptionMessage('Versions file not found');
        $method->invoke($this->action, $release, $repository);
    }

    public function testVersionSeriesNotFoundError()
    {
        // Create test versions file without the expected version series
        $testVersionsContent = <<<'PHP'
<?php

return [
    'minor-versions' => [
        '1.1' => [
            '1.1.28' => 'February 28, 2023',
        ],
    ],
];
PHP;
        
        file_put_contents($this->testVersionsPath, $testVersionsContent);
        
        $release = [
            'tag_name' => '2.0.55',
            'published_at' => '2024-07-29T10:30:00Z'
        ];
        
        $repository = [
            'full_name' => 'yiisoft/yii2'
        ];
        
        $method = new \ReflectionMethod($this->action, 'updateVersionsFile');
        $method->setAccessible(true);
        
        $this->expectException(\yii\base\Exception::class);
        $this->expectExceptionMessage("Could not find version series '2.0' in versions file");
        $method->invoke($this->action, $release, $repository);
    }
}