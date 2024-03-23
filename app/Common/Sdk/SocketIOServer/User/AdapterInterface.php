<?php

namespace App\Common\Sdk\SocketIOServer\User;

interface AdapterInterface
{
    /**
     * add adds a known uid to one sid.
     */
    public function add(int $uid, string $sid);

    /**
     * del removes a sid from multiple rooms. If none of the room is
     * given, the sid will be removed from all rooms.
     */
    public function del(string $sid, string ...$rooms);

    /**
     * broadcast sends a packet out based the options specified in $opts.
     * @param mixed $packet
     * @param mixed $opts
     */
    public function broadcast($packet, $opts);

    /**
     * clients method lists all sids in the given rooms, using junction.
     */
    public function clients(string ...$rooms): array;

    /**
     * clientRooms method lists all rooms a given sid has joined.
     */
    public function clientRooms(string $sid): array;
}