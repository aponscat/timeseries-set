<?php

namespace Apons\TimeSeriesSet\Adapters\Array;

use Apons\TimeSeriesSet\Interfaces\SetInterface;
use Apons\TimeSeriesSet\Interfaces\SetStorageInterface;

class ArraySet implements SetInterface{

  private $s;

  public function __construct (SetStorageInterface $s){
    $this->s=$s;
  }

  public function clear() {
    $this->s->clear();
  }

  public function add (string $dateString, string $tag): void
  {
    $this->s->setOrIncrement("$dateString:$tag");
    $this->s->setOrAppend($dateString, $tag);
  }

  public function getAllTagsInTime(string $dateString): ?array
  {
    $dateValues=$this->s->get($dateString);
    if ($dateValues)
    {
        $result=[];
        $tags=explode(',', $dateValues);
        foreach ($tags as $tag)
        {
            $result[$tag]=$this->s->get("$dateString:$tag");
        }
        return $result;
    }
    
    return null;
  }

}