<?php

namespace Apons\TimeSeriesSet\Adapters\Array;

use Apons\TimeSeriesSet\Interfaces\SetStorageInterface;

class ArraySetStorage implements SetStorageInterface {

  private array $datesTags=[];
  private array $tagsByDate=[];

  public function __construct()
  {
    $this->clear();
  }

  public function clear(): void {
    $this->datesTags=[];
    $this->tagsByDate=[];
  }

  public function setOrIncrement (string $key): void {
    if (isset($this->datesTags[$key]))
    {
      $this->datesTags[$key]++;
    }
    else
    {
      $this->datesTags[$key]=1;
    }
  }

  public function setOrAppend(string $key, string $value): void
  {
    if (isset($this->tagsByDate[$key]))
    {
        $this->tagsByDate[$key].=','.$value;
    }
    else
    {
        $this->tagsByDate[$key]=$value;
    }
  }

  public function get(string $key): ?string
  {
    if (stripos($key, ':')===false)
    {
        if (isset($this->tagsByDate[$key]))
        {
            return $this->tagsByDate[$key];
        }
    }
    else
    {
        if (isset($this->datesTags[$key]))
        {
            return $this->datesTags[$key];
        }
    }
    return null;
  }

}