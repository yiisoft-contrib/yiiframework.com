<?php

use Http\Client\HttpClient;
use Http\Discovery\Strategy\DiscoveryStrategy;
use Psr\Http\Client\ClientInterface;

final class GithubProgressHttpDiscovery implements DiscoveryStrategy
{
    public static $client;

    public static function getCandidates($type)
    {
        if ($type !== HttpClient::class && $type !== ClientInterface::class) {
            return [];
        }

        return [['class' => static function () { return self::$client; }]];
    }
}
