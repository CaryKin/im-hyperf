<?php

namespace App\Common\Cache;


use Psr\SimpleCache\InvalidArgumentException;

class SocketIOCache extends BaseCache
{
    const Token = "ws:fd:%s";

    /**
     * @throws InvalidArgumentException
     */
    public static  function setFd($key, $value, $ttl = 3600): bool
    {
        $k = self::getKey(self::Token, $key);
        return self::client()->set($k, $value, $ttl);
    }
}