<?php

namespace Apons\TimeSeriesSet\Interfaces;

interface SetStorageInterface {
  public function clear(): void;
  public function setOrIncrement (string $key): void;
  public function setOrAppend(string $key, string $value): void;
  public function get(string $key): ?string;
}