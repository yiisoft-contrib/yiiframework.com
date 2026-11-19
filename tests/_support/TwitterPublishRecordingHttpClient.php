<?php

declare(strict_types=1);

use Abraham\TwitterOAuth\HttpClientInterface;

/**
 * Recording HTTP boundary for TwitterService::publish() tests. Only the
 * transport is replaced; the real TwitterOAuth client builds the URL,
 * OAuth signature, headers and body itself.
 */
final class TwitterPublishRecordingHttpClient implements HttpClientInterface
{
    public string $url = '';
    public string $method = '';
    public array $headers = [];
    public string $body = '';
    public int $responseCode = 201;

    public function request(string $url, string $method, array $headers, string $body, array $curlOptions): array
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;

        $sentValid = $url === 'https://api.x.com/2/tweets'
            && json_decode($body, true) === ['text' => 'hello world'];

        return [
            'body' => $sentValid ? '{"data":{"id":"1","text":"hello world"}}' : '{"title":"Not Found"}',
            'headers' => [],
            'httpCode' => $sentValid ? $this->responseCode : 404,
        ];
    }
}
