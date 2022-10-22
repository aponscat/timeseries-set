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

    public function set(string $key, string $value, int $ttl) {
        $this->keys[$key]=$value;
    }

    public function increment($key) {
        $num=(int)$this->get($key);
        $num=$num+1;
        $this->set($key, $num, $this->ttl);
    }

    public function append(string $key, string $value) {
        $currentValue=$this->get($key);
        $this->set($key, $currentValue.$value, $this->ttl);
    }

}