<?php

namespace App\Common\Sdk\SocketIOServer;

use Hyperf\SocketIOServer\Emitter\Emitter;

trait ReplenishEmitter
{
    use Emitter;
    public function ti(int|string $userId)
    {
        $copy = clone $this;
        $copy->to[] = (string) $room;
        return $copy;
    }
}