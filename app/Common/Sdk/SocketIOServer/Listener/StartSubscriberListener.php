<?php

namespace App\Common\Sdk\SocketIOServer\Listener;

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
        foreach (SocketIORouter::get('forward') ?? [] as $class) {
            $instance = $this->container->get($class);
            $adapter = $instance->getAdapter();
            if ($adapter instanceof RedisAdapter) {
                $adapter->subscribe();
            }
            if ($adapter instanceof EphemeralInterface) {
                $adapter->cleanUpExpired();
            }
        }
    }
}