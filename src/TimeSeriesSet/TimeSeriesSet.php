<?php

namespace Apons\TimeSeriesSet;

class TimeSeriesSet {

  private string $datePattern;
  private CacheSetInterface $cacheSet;

  public function __construct (CacheSetInterface $cacheSet, string $datePattern='YmdHi')
  {
    $this->cacheSet=$cacheSet;
    $this->datePattern=$datePattern;
  }

  public function clear() {
    $this->cacheSet->clear();
  }

  public function add (string|int $tag, int $time=null): void
  {
    $dateString=$this->getDateString($time);
    $this->cacheSet->add($dateString, $tag);
  }

  public function getAllTagsInTime(int $time=null): ?array
  {
    $dateString=$this->getDateString($time);
    return $this->cacheSet->getAllTagsInTime($dateString);
  }

  private function getDateString (int $time=null): string
  {
    if ($time==null)
    {
        $time=time();
    }
    return date($this->datePattern, $time);
  }

}