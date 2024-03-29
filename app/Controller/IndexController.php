<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;





use App\Common\Cache\SocketIOCache;
use App\Log;
use Psr\SimpleCache\InvalidArgumentException;

class IndexController extends AbstractController
{

    /**
     * @throws InvalidArgumentException
     */
    public function index()
    {
        $io = \Hyperf\Context\ApplicationContext::getContainer()->get(\Hyperf\SocketIOServer\SocketIO::class);
        $io->to('socketId')->emit('hey', 'I just met you');
        return $this->response->json([
            "data" => 111
        ]);
    }
}
