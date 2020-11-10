<?php

namespace App\Utils\Fetchers;

abstract class HtmlFetcher
{
    const CLIENT_BROWERSHOT = 'browsershot';
    const CLIENT_CURL = 'curl';
    const CLIENT_GUZZLE = 'guzzle';

    const CLIENTS = [
        self::CLIENT_BROWERSHOT,
        self::CLIENT_CURL,
        self::CLIENT_GUZZLE,
    ];

    const NAME = 'not-set';

    abstract public function fetchHtml(string $url): string;

    public static function satisfiedBy(string $client): bool
    {
        return $client === static::NAME;
    }
}
