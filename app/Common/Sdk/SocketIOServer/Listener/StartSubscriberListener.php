<?php

namespace App\Common\Sdk\SocketIOServer\Listener;

use App\Common\Sdk\SocketIOServer\TestAdapter;
use App\Log;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\Server\Event\MainCoroutineServerStart;
use Hyperf\SocketIOServer\Collector\SocketIORouter;
use Hyperf\SocketIOServer\Room\EphemeralInterface;
use Hyperf\SocketIOServer\Room\RedisAdapter;
use Psr\Container\ContainerInterface;

class StartSubscriberListener implements ListenerInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            MainWorkerStart::class,
            MainCoroutineServerStart::class,
        ];
    }

    public function process(object $event): void
    {
        $a = json_encode(SocketIORouter::get('forward'));
        Log::get()->info($a);
        foreach (SocketIORouter::get('forward') ?? [] as $class) {
            $instance = $this->container->get($class);
            $adapter = $instance->getAdapter();
            Log::get()->info(11);
            Log::get()->info($class);
            if ($adapter instanceof RedisAdapter) {
                Log::get()->info(22);
                $adapter->subscribe();
            }
            if ($adapter instanceof TestAdapter) {
                Log::get()->info(7777);
                $adapter->subscribe();
            }
            if ($adapter instanceof EphemeralInterface) {
                Log::get()->info(33);
                $adapter->cleanUpExpired();
            }
        }
    }
}