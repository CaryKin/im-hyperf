<?php

namespace App\Common\Sdk\SocketIOServer\User;

interface AdapterInterface
{
    /**
     * add adds a known uid to one sid.
     */
    public function add(string $uid, string $sid);

    /**
     * del removes a sid from multiple rooms. If none of the room is
     * given, the sid will be removed from all rooms.
     */
    public function del(string $uid, string $sid);
}