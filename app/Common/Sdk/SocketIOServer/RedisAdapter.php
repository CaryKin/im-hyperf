<?php

namespace App\Common\Sdk\SocketIOServer;

use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Coroutine\Coroutine;

class RedisAdapter extends \Hyperf\SocketIOServer\Room\RedisAdapter
{
    public function bind(string $sid, string $uid): void
    {
        $this->add($sid, "U_" . $uid);
    }

    public function getSidFromUid(string|int $uid)
    {
        $clientSids = $this->redis->sMembers($this->getUidKey($uid));
        return $clientSids ?: [];
    }

    public function unBind(string|int $uid, string $sid = "")
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
        $sids = $this->redis->zRangeByScore($this->getExpireKey(), '-inf', (string) (microtime(true) * 1000));

        if (! empty($sids)) {
            foreach ($sids as $sid) {
                $this->del($sid);
            }

            $this->redis->zRem($this->getExpireKey(), ...$sids);
        }

        // TODO: Redis doesn't provide atomicity. It may be necessary to use a lock here.
        $uids = $this->redis->zRangeByScore($this->getExpireKey(), '-inf', (string) (microtime(true) * 1000));
        if (! empty($uids)) {
            foreach ($uids as $uid) {
                $this->del($uid);
            }

            $this->redis->zRem($this->getUidExpireKey(), ...$uids);
        }
    }
    protected function getUidExpireKey(): string
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