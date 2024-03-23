<?php

namespace App\Common\Sdk\SocketIOServer\User;

use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Coroutine\Coroutine;
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
    protected \Hyperf\Redis\Redis|RedisProxy $redis;

    protected int $ttl = 0;

    public function __construct(RedisFactory $redis, protected Sender $sender, protected NamespaceInterface $nsp, protected SidProviderInterface $sidProvider)
    {
        $this->redis = $redis->get($this->connection);
    }

    public function add(string $uid, string $sid)
    {
        $this->redis->multi();
        $this->redis->sAdd($this->getUidKey($uid), $sid);
        $this->redis->zAdd($this->getExpireKey(), microtime(true) * 1000 + $this->ttl, $uid);
        $this->redis->exec();
    }

    public function del(string $uid, string $sid = "")
    {
        if (empty($sid)) {
            $clientSids = $this->redis->sMembers($this->getUidKey($uid));
            if (empty($clientSids)) {
                return;
            }
            $this->del($uid, $sid);
            $this->redis->multi();
            $this->redis->del($this->getUidKey($uid));
            $this->redis->exec();
            return;
        }
        $this->redis->multi();
        $this->redis->sRem($this->getUidKey($uid), $sid);
        $this->redis->exec();
    }

    public function cleanUpExpired(): void
    {
        Coroutine::create(function () {
            while (true) {
                if (CoordinatorManager::until(Constants::WORKER_EXIT)->yield($this->cleanUpExpiredInterval / 1000)) {
                    break;
                }
                $this->cleanUpExpiredOnce();
            }
        });
    }

    public function cleanUpExpiredOnce(): void
    {
        // TODO: Redis doesn't provide atomicity. It may be necessary to use a lock here.
        $uids = $this->redis->zRangeByScore($this->getExpireKey(), '-inf', (string) (microtime(true) * 1000));

        if (! empty($uids)) {
            foreach ($uids as $uid) {
                $this->del($uid);
            }

            $this->redis->zRem($this->getExpireKey(), ...$uids);
        }
    }
    protected function getExpireKey(): string
    {
        return join(':', [
            $this->redisPrefix,
            $this->nsp->getNamespace(),
            'uid_expire',
        ]);
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