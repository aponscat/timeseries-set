<?php

namespace Apons\TimeSeriesSet;

class MemcachedCacheSet implements CacheSetInterface {
  private int $expiry=60;
  private \Memcached $m;

  public function __construct()
  {
    $m = new \Memcached();
    $m->addServer('localhost', 11211);
    $m->setOption(\Memcached::OPT_COMPRESSION, false);
    $this->m=$m;
  }

  public function clear()
  {
    $this->m->flush();
  }

  public function add (string $dateString, string $tag): void
  {

    if(!$this->m->get($dateString.':'.$tag))
    {
      $this->m->set($dateString.':'.$tag, 1, $this->expiry);
    }
    else
    {
      $this->m->increment($dateString.':'.$tag);
    }

    if(!$this->m->get($dateString))
    {
      $this->m->set($dateString, $tag, $this->expiry);
    }
    else
    {
      $this->m->append($dateString, ",$tag");
    }
  }

  public function getAllTagsInTime(string $dateString): ?array
  {
    $allTagsInTime=$this->m->get($dateString);
    if ($allTagsInTime)
    {
        $result=[];
        $tags=explode(',', $allTagsInTime);
        foreach ($tags as $tag)
        {
            $result[$tag]=$this->m->get("$dateString:$tag");
        }
        return $result;
    }
    
    return null;
  }

}