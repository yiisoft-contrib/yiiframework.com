<?php

use Http\Client\HttpClient;
use Http\Discovery\Strategy\DiscoveryStrategy;
use Psr\Http\Client\ClientInterface;

final class GithubProgressHttpDiscovery implements DiscoveryStrategy
{
    public static $client;

    public static function getCandidates($type)
    {
        // github-api 2.x asks the httplug discovery (Http\Client\HttpClient);
        // github-api 3.x asks the PSR-18 discovery (Psr\Http\Client\ClientInterface).
        // Both share the same strategy queue, so answer both types.
        if ($type !== HttpClient::class && $type !== ClientInterface::class) {
            return [];
        }

        return [['class' => static function () { return self::$client; }]];
    }
}
