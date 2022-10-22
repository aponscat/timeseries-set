<?php

namespace Apons\TimeSeriesSet\Adapters\LaravelCache;


class LaravelCacheMock {

    private array $keys=[];
    private int $ttl;

    public function __construct (int $ttl=3600) {
        $this->ttl=$ttl;
        $this->flush();
    }

    public function flush(): void {
        $this->keys=[];
    }

    public function get(string $key): ?string {
        if (isset($this->keys[$key]))
        {
            return $this->keys[$key];
        }
        return false;
    }

    public function set(string $key, string $value, int $ttl=3600) {
        $this->keys[$key]=$value;
    }

}