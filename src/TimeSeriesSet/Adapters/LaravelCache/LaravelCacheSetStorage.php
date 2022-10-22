<?php

namespace Apons\TimeSeriesSet\Adapters\LaravelCache;

use Apons\TimeSeriesSet\Interfaces\SetStorageInterface;

class LaravelCacheSetStorage implements SetStorageInterface {
    private int $expiry=3600;
    private $m;
  
    public function __construct($m, $expiry=3600)
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
        $num=(int)$this->m->get($key);
        $num=$num+1;
        $this->m->set($key, $num, $this->expiry);
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
        $current=$this->m->get($key);
        $this->m->set($key, $current.",$value", $this->expiry);
      }
    }
  
    public function get(string $key): ?string
    {
      return $this->m->get($key);
    }
  
  }