<?php

namespace App\Common\Sdk\SocketIOServer;

use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\SocketIOServer\SocketIOConfig;
use Hyperf\WebSocketServer\Sender;

class LineNamespace extends BaseNamespace
{
    public $userAdapter;

    public function __construct(Sender $sender, SidProviderInterface $sidProvider, ?SocketIOConfig $config = null)
    {
        parent::__construct($sender, $sidProvider, $config);
    }
}