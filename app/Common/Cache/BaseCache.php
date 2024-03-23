<?php

namespace App\Common\Cache;



use Hyperf\Context\ApplicationContext;
use Psr\SimpleCache\CacheInterface;

class BaseCache
{

    public static function client()
    {
        return ApplicationContext::getContainer()->get(CacheInterface::class);
    }

    public static function getKey($k, ...$params): string
    {
        return sprintf($k, ...$params);
    }
}