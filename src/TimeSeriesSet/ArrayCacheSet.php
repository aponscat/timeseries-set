<?php

namespace Apons\TimeSeriesSet;

class ArrayCacheSet implements CacheSetInterface{

  private array $datesTags=[];
  private array $tagsByDate=[];

  public function clear() {
    $this->datesTags=[];
    $this->tagsByDate=[];
  }

  public function add (string $dateString, string $tag): void
  {
    if (isset($this->datesTags[$dateString.':'.$tag]))
    {
      $this->datesTags[$dateString.':'.$tag]++;
    }
    else
    {
      $this->datesTags[$dateString.':'.$tag]=1;
    }

    if (isset($this->tagsByDate[$dateString]))
    {
        $this->tagsByDate[$dateString].=','.$tag;
    }
    else
    {
        $this->tagsByDate[$dateString]=$tag;
    }
  }

  public function getAllTagsInTime(string $dateString): ?array
  {
    if (isset($this->tagsByDate[$dateString]))
    {
        $result=[];
        $tags=explode(',', $this->tagsByDate[$dateString]);
        foreach ($tags as $tag)
        {
            $result[$tag]=$this->datesTags["$dateString:$tag"];
        }
        return $result;
    }
    
    return null;
  }

}