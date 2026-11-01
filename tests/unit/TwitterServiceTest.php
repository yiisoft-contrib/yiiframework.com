<?php

use Abraham\TwitterOAuth\HttpClientInterface;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Regression tests for the twitteroauth 9.x migration.
 *
 * twitteroauth 9 defaults `post()` to a JSON payload (`jsonPayload => true`).
 * The Twitter status endpoint expects a form-encoded body, so the service
 * must keep passing `['jsonPayload' => false]`. These tests lock that wire
 * format contract without touching the network.
 */
class TwitterServiceTest extends \Codeception\Test\Unit
{
    private function newClientWith(TwitterServiceTestHistoryClient $client)
    {
        // 4-arg credentials + injected HTTP client (the twitteroauth 9.x constructor).
        return new TwitterOAuth('consumer-key', 'consumer-secret', 'access-token', 'access-secret', $client);
    }

    public function testPostSendStatusFormEncodedNotJson(): void
    {
        $client = new TwitterServiceTestHistoryClient();
        $this->newClientWith($client)->post('statuses/update', ['status' => 'hello world'], ['jsonPayload' => false]);

        // Form-encoded body, not a JSON object.
        self::assertSame('status=hello%20world', $client->body);
        self::assertStringNotContainsString('{', $client->body);
    }

    public function testDefaultPostWouldBeJsonUnlessOverridden(): void
    {
        // Documents why we must pass jsonPayload=false: the 9.x default is JSON.
        $client = new TwitterServiceTestHistoryClient();
        $this->newClientWith($client)->post('statuses/update', ['status' => 'hello world']);

        self::assertStringContainsString('{"status":"hello world"}', $client->body);
        self::assertStringContainsString('application/json', implode("\n", $client->headers));
    }

    public function testOAuthHeadersUseProvidedCredentials(): void
    {
        $client = new TwitterServiceTestHistoryClient();
        $this->newClientWith($client)->post('statuses/update', ['status' => 'x'], ['jsonPayload' => false]);

        $auth = $this->authorizationHeader($client);
        self::assertStringContainsString('oauth_consumer_key="consumer-key"', $auth);
        self::assertStringContainsString('oauth_token="access-token"', $auth);
    }

    public function testLastHttpCodeIsIntFromClient(): void
    {
        $client = new TwitterServiceTestHistoryClient(500);
        $twitter = $this->newClientWith($client);
        $twitter->post('statuses/update', ['status' => 'x'], ['jsonPayload' => false]);

        self::assertSame(500, $twitter->getLastHttpCode());
    }

    private function authorizationHeader(TwitterServiceTestHistoryClient $client): string
    {
        foreach ($client->headers as $header) {
            if (stripos($header, 'Authorization:') === 0) {
                return $header;
            }
        }
        self::fail('Authorization header was not emitted.');
    }
}

/**
 * Records the outgoing request and returns a canned JSON response.
 */
class TwitterServiceTestHistoryClient implements HttpClientInterface
{
    public string $url = '';
    public array $headers = [];
    public string $body = '';
    public int $lastCode;

    public function __construct(int $httpCode = 200)
    {
        $this->lastCode = $httpCode;
    }

    public function request(string $url, string $method, array $headers, string $body, array $curlOptions): array
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;

        return ['body' => '{"id":1,"text":"sent"}', 'headers' => [], 'httpCode' => $this->lastCode];
    }
}
