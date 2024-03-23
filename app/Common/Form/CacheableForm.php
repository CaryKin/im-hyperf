<?php

namespace App\Common\Form;

class CacheableForm
{

    /**
     * @param null|int $ttl the max offset for ttl
     */
    public function __construct(
        public ?string $prefix = null,
        public ?string $value = null,
        public ?int $ttl = null,
        public ?string $listener = null,
        public int $offset = 0,
        public bool $collect = false,
        public ?array $skipCacheResults = null,
        public mixed $data = null
    ) {
    }
}