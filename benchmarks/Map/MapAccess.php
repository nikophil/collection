<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use PhpBench\Attributes\Revs;

/**
 * Key-based reads, run over a batch of keys spread across the map.
 */
trait MapAccess
{
	public function benchGet(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map->get($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $sum;
	}

	public function benchOffsetGet(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map[$this->keys[intdiv($i * $count, self::Batch)]];
		}

		return $sum;
	}

	public function benchGetOrNull(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map->getOrNull($this->keys[intdiv($i * $count, self::Batch)]) ?? 0;
		}

		return $sum;
	}

	public function benchGetOrDefault(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map->getOrDefault($this->keys[intdiv($i * $count, self::Batch)], 0);
		}

		return $sum;
	}

	public function benchGetOrCompute(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map->getOrCompute($this->keys[intdiv($i * $count, self::Batch)], static fn (): int => 0);
		}

		return $sum;
	}

	public function benchContainsKey(): int
	{
		$found = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) $this->map->containsKey($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $found;
	}

	public function benchOffsetExists(): int
	{
		$found = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) isset($this->map[$this->keys[intdiv($i * $count, self::Batch)]]);
		}

		return $found;
	}

	/**
	 * Values are not indexed: a single lookup scans half of the map.
	 */
	public function benchContainsValue(): bool
	{
		return $this->map->containsValue($this->probeValue);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchIsEmpty(): bool
	{
		return $this->map->isEmpty();
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchIsNotEmpty(): bool
	{
		return $this->map->isNotEmpty();
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchCount(): int
	{
		return $this->map->count();
	}
}
