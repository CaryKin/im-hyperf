<?php

namespace App\Common\Sdk\SocketIOServer;

class Socket extends \Hyperf\SocketIOServer\Socket
{
    public function bind(string $fd, string|int $userId)
    {
        $userId = "U_" . $userId;
        $this->adapter->add($fd, $userId);
    }

    public function close(string|int $userId)
    {
        $userId = "U_" . $userId;
        $this->adapter->del($this->getSid(), $userId);
    }

    public function join(string ...$rooms)
    {
        $prefix = 'R_';
        $rooms = array_map(function($value) use ($prefix) {
            return $prefix . $value;
        }, $rooms);
        $this->adapter->add($this->getSid(), ...$rooms);
    }

    public function leave(string ...$rooms)
    {
        $prefix = 'R_';
        $rooms = array_map(function($value) use ($prefix) {
            return $prefix . $value;
        }, $rooms);
        $this->adapter->del($this->getSid(), ...$rooms);
    }


}