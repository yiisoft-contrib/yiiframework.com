<?php

use app\components\contentShare\services\TwitterService;

/**
 * Regression tests for the twitteroauth 9.x migration.
 *
 * The main regression exercises the real TwitterService::publish() end to
 * end through the actual upgraded TwitterOAuth client and dummy
 * credentials; only its HTTP transport boundary is replaced by a recording
 * client. This catches the API v1 -> v2 wire contract (previously the
 * obsolete /2/statuses/update form endpoint with 200 handling).
 */
class TwitterServiceTest extends \Codeception\Test\Unit
{
    private $previousApp;
    private $previousAliases;

    protected function setUp(): void
    {
        $this->previousApp = Yii::$app;
        $this->previousAliases = Yii::$aliases;

        Yii::$app = new yii\console\Application([
            'id' => 'twitter-service-test',
            'basePath' => dirname(__DIR__, 2),
            'params' => [
                'twitter.consumerKey' => 'consumer-key',
                'twitter.consumerSecret' => 'consumer-secret',
                'twitter.accessToken' => 'access-token',
                'twitter.accessTokenSecret' => 'access-secret',
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Yii::$app = $this->previousApp;
        Yii::$aliases = $this->previousAliases;
    }

    public function testPublishUsesApiV2CreateContract(): void
    {
        $client = new \TwitterPublishRecordingHttpClient();

        $published = (new TwitterService(new \TwitterPublishContentShare()))->publish($client);

        self::assertTrue($published);
        self::assertSame('https://api.x.com/2/tweets', $client->url);
        self::assertSame('POST', $client->method);
        self::assertSame(['text' => 'hello world'], json_decode($client->body, true));
        self::assertStringContainsString('application/json', implode("\n", $client->headers));
    }

    public function testPublishFailsWhenCreateNotAccepted(): void
    {
        // The recording transport answers 404 for the v2 create shape when the
        // response code is not 201, so a wrong status must not succeed.
        $client = new \TwitterPublishRecordingHttpClient();
        $client->responseCode = 404;

        $published = (new TwitterService(new \TwitterPublishContentShare()))->publish($client);

        self::assertFalse($published);
        self::assertSame('https://api.x.com/2/tweets', $client->url);
    }

    public function testPublishFailsWhenRequestIsRejected(): void
    {
        // A create request that does not match the v2 shape is answered 404
        // by the transport; the service must report failure, not success.
        $contentShare = new \TwitterPublishContentShare();
        $contentShare->message = 'hello world?';
        $client = new \TwitterPublishRecordingHttpClient();

        $published = (new TwitterService($contentShare))->publish($client);

        self::assertFalse($published, 'a non-201 create answer must not succeed');
        self::assertSame('https://api.x.com/2/tweets', $client->url);
        self::assertSame(['text' => 'hello world?'], json_decode($client->body, true));
    }
}
