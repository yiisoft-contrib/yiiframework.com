<?php

use Http\Client\HttpClient;
use Http\Discovery\Strategy\DiscoveryStrategy;

final class GithubProgressHttpDiscovery implements DiscoveryStrategy
{
    public static $client;

    public static function getCandidates($type)
    {
        if ($type !== HttpClient::class) {
            return [];
        }

        return [['class' => static function () { return self::$client; }]];
    }
}
