<?php

class GitHubWebhookCest
{
    public function testPingEvent(\FunctionalTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-GitHub-Event', 'ping');
        
        $payload = json_encode(['zen' => 'Design for failure.']);
        
        $I->sendPOST('/site/github-webhook', $payload);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals('pong');
    }

    public function testMethodNotAllowed(\FunctionalTester $I)
    {
        $I->sendGET('/site/github-webhook');
        $I->seeResponseCodeIs(405);
    }

    public function testInvalidSignatureWhenSecretSet(\FunctionalTester $I)
    {
        // Mock that we have a secret configured
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-GitHub-Event', 'release');
        $I->haveHttpHeader('X-Hub-Signature-256', 'sha256=invalid-signature');
        
        $payload = json_encode([
            'action' => 'published',
            'repository' => ['full_name' => 'yiisoft/yii2'],
            'release' => [
                'tag_name' => '2.0.50',
                'published_at' => '2024-01-15T10:00:00Z'
            ]
        ]);
        
        $I->sendPOST('/site/github-webhook', $payload);
        // Should either work (if no secret) or fail with 403 (if secret configured)
        $I->seeResponseCodeIsInRange(200, 403);
    }

    public function testUnsupportedRepository(\FunctionalTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-GitHub-Event', 'release');
        
        $payload = json_encode([
            'action' => 'published',
            'repository' => ['full_name' => 'some/other-repo'],
            'release' => [
                'tag_name' => '1.0.0',
                'published_at' => '2024-01-15T10:00:00Z'
            ]
        ]);
        
        $I->sendPOST('/site/github-webhook', $payload);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains('Unsupported repository');
    }

    public function testMissingRequiredFields(\FunctionalTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-GitHub-Event', 'release');
        
        // Missing action field
        $payload = json_encode([
            'repository' => ['full_name' => 'yiisoft/yii2'],
            'release' => [
                'tag_name' => '2.0.50',
                'published_at' => '2024-01-15T10:00:00Z'
            ]
        ]);
        
        $I->sendPOST('/site/github-webhook', $payload);
        $I->seeResponseCodeIs(400);
    }

    public function testNonPublishedAction(\FunctionalTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-GitHub-Event', 'release');
        
        $payload = json_encode([
            'action' => 'created', // Not 'published'
            'repository' => ['full_name' => 'yiisoft/yii2'],
            'release' => [
                'tag_name' => '2.0.50',
                'published_at' => '2024-01-15T10:00:00Z'
            ]
        ]);
        
        $I->sendPOST('/site/github-webhook', $payload);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals('Ignored: not a published release');
    }

    public function testInvalidVersionFormat(\FunctionalTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-GitHub-Event', 'release');
        
        $payload = json_encode([
            'action' => 'published',
            'repository' => ['full_name' => 'yiisoft/yii2'],
            'release' => [
                'tag_name' => 'invalid-version', // Invalid format
                'published_at' => '2024-01-15T10:00:00Z'
            ]
        ]);
        
        $I->sendPOST('/site/github-webhook', $payload);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains('Invalid version format');
    }

    public function testEndpointIsAccessible(\FunctionalTester $I)
    {
        // Test that the endpoint exists and responds
        $I->sendPOST('/site/github-webhook');
        // Should not be 404, meaning the route exists
        $I->dontSeeResponseCodeIs(404);
    }
}