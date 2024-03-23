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
use Psr\SimpleCache\InvalidArgumentException;

class IndexController extends AbstractController
{

    /**
     * @throws InvalidArgumentException
     */
    public function index()
    {

        SocketIOCache::setFd('ddd',"ws_fd:65f9aaf3e5de5#2036");
        return $this->response->json([
            "data" => 111
        ]);
    }
}
