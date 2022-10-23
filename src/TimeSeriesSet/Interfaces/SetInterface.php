<?php

namespace Apons\TimeSeriesSet\Interfaces;

interface SetInterface {
  public function __construct(SetStorageInterface $s);
  public function clear();
  public function increment (string $dateString, string $tag): void;
  public function add (string $dateString, string $tag): void;
  public function getAllTagsInTime(string $dateString): ?array;
}