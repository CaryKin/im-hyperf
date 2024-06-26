<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Cache\SocketIOCache;
use App\Common\Form\CacheableForm;
use App\Common\Sdk\SocketIOServer\RedisAdapter;
//use App\Common\Sdk\SocketIOServer\Socket;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\Annotation\SocketIONamespace;
use Hyperf\SocketIOServer\BaseNamespace;

use Hyperf\Codec\Json;
use Hyperf\SocketIOServer\Socket;


#[SocketIONamespace("/")]
class SocketIOController extends BaseNamespace
{
    public $a = 1;

    #[Event("connect")]
    public function onConnect(Socket $socket)
    {
        $this->a += 1;
        // 当有客户端连接时触发此方法
        $fd = $socket->getSid();
//        $socket->bind($fd, $this->a);
        echo "Client connected: $fd\n";
    }

    /**
     * @param string $data
     */
    #[Event("event")]
    public function onEvent(Socket $socket, $data)
    {
        // 应答
        return 'Event Received: ' . $data;
    }

    /**
     * @param string $data
     */
    #[Event("join-room")]
    public function onJoinRoom(Socket $socket, $data)
    {
        // 将当前用户加入房间
        $socket->join($data);
        // 向房间内其他用户推送（不含当前用户）
        $socket->to($data)->emit('event', $socket->getSid() . "has joined {$data}");
        // 向房间内所有人广播（含当前用户）
        $this->emit('event', 'There are ' . count($socket->getAdapter()->clients($data)) . " players in {$data}");
    }

    /**
     * @param string $data
     */
    #[Event("say")]
    public function onSay(Socket $socket, $data)
    {
        $data = Json::decode($data);
        $socket->to($data['room'])->emit('event', $socket->getSid() . " say: {$data['message']}");
    }

    #[Event("disconnect")]
    public function onDisconnect(Socket $socket)
    {
        // 当有客户端连接时触发此方法
        $fd = $socket->getSid();
        echo "Client close: $fd\n";
    }
}