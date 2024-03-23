<?php

namespace App\Common\Sdk\SocketIOServer\User;

use Hyperf\Redis\RedisFactory;
use Hyperf\Redis\RedisProxy;
use Hyperf\SocketIOServer\NamespaceInterface;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\WebSocketServer\Sender;

class RedisAdapter implements AdapterInterface
{
    protected string $redisPrefix = 'ws';

    protected int $retryInterval = 1000;

    protected int $cleanUpExpiredInterval = 30000;

    protected string $connection = 'default';

    /**
     * @var \Hyperf\Redis\Redis|RedisProxy
     */
    protected $redis;

    protected int $ttl = 0;

    public function __construct(RedisFactory $redis, protected Sender $sender, protected NamespaceInterface $nsp, protected SidProviderInterface $sidProvider)
    {
        $this->redis = $redis->get($this->connection);
    }

    public function add(int $uid, string $sid)
    {
        $this->redis->multi();
        $this->redis->sAdd($this->getUidKey($uid), $sid);
        foreach ($rooms as $room) {
            $this->redis->sAdd($this->getRoomKey($room), $sid);
            $this->redis->zAdd($this->getExpireKey(), microtime(true) * 1000 + $this->ttl, $sid);
        }
        $this->redis->sAdd($this->getStatKey(), $sid);
        $this->redis->exec();
    }

    public function del(string $sid, string ...$rooms)
    {
        // TODO: Implement del() method.
    }

    public function broadcast($packet, $opts)
    {
        // TODO: Implement broadcast() method.
    }

    public function clients(string ...$rooms): array
    {
        // TODO: Implement clients() method.
    }

    public function clientRooms(string $sid): array
    {
        // TODO: Implement clientRooms() method.
    }

    protected function getUidKey(string $uid): string
    {
        return join(':', [
            $this->redisPrefix,
            $this->nsp->getNamespace(),
            'uid',
            $uid,
        ]);
    }
}