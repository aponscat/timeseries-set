<?php

namespace Apons\TimeSeriesSet;

class MemcachedSetStorage implements SetStorageInterface {
  private int $expiry=3600;
  private \Memcached $m;

  public function __construct(\Memcached $m, $expiry=3600)
  {
    $this->m=$m;
    $this->expiry=$expiry;
  }

  public function clear(): void
  {
    $this->m->flush();
  }

  public function setOrIncrement (string $key): void {
    if(!$this->m->get($key))
    {
      $this->m->set($key, 1, $this->expiry);
    }
    else
    {
      $this->m->increment($key);
    }
  }

  public function setOrAppend(string $key, string $value): void
  {
    if(!$this->m->get($key))
    {
      $this->m->set($key, $value, $this->expiry);
    }
    else
    {
      $this->m->append($key, ",$value");
    }
  }

  public function get(string $key): ?string
  {
    return $this->m->get($key);
  }

}