<?php

namespace Apons\TimeSeriesSet;

interface CacheSetInterface {
  public function clear();
  public function add (string $dateString, string $tag): void;
  public function getAllTagsInTime(string $dateString): ?array;
}