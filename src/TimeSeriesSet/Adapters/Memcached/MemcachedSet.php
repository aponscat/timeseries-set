<?php

namespace Apons\TimeSeriesSet\Adapters\Memcached;

use Apons\TimeSeriesSet\Interfaces\SetInterface;
use Apons\TimeSeriesSet\Interfaces\SetStorageInterface;

class MemcachedSet implements SetInterface {
  private SetStorageInterface $s;

  public function __construct(SetStorageInterface $s)
  {
    $this->s=$s;
  }

  public function clear()
  {
    $this->s->clear();
  }

  public function add (string $dateString, string $tag): void
  {
    $this->s->setOrIncrement("$dateString:$tag");
    $this->s->setOrAppend($dateString, $tag);
  }

  public function getAllTagsInTime(string $dateString): ?array
  {
    $allTagsInTime=$this->s->get($dateString);
    if ($allTagsInTime)
    {
        $result=[];
        $tags=explode(',', $allTagsInTime);
        foreach ($tags as $tag)
        {
            $result[$tag]=$this->s->get("$dateString:$tag");
        }
        return $result;
    }
    
    return null;
  }

}