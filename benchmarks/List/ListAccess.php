<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Map\ImmutableMap;

/**
 * Index-based reads. Constant-time lookups run over a batch of indexes spread across the list,
 * searches look for the element in the middle of it.
 */
trait ListAccess
{
	public function benchGet(): int
	{
		$sum = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->collection->get(intdiv($i * $count, self::Batch));
		}

		return $sum;
	}

	public function benchGetOrNull(): int
	{
		$sum = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->collection->getOrNull(intdiv($i * $count, self::Batch)) ?? 0;
		}

		return $sum;
	}

	public function benchOffsetGet(): int
	{
		$sum = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->collection[intdiv($i * $count, self::Batch)];
		}

		return $sum;
	}

	public function benchGetOrDefault(): int
	{
		$sum = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->collection->getOrDefault(intdiv($i * $count, self::Batch), 0);
		}

		return $sum;
	}

	public function benchGetOrCompute(): int
	{
		$sum = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->collection->getOrCompute(intdiv($i * $count, self::Batch), static fn (): int => 0);
		}

		return $sum;
	}

	public function benchIndexOf(): int
	{
		return $this->collection->indexOf($this->probe);
	}

	public function benchLastIndexOf(): int
	{
		return $this->collection->lastIndexOf($this->probe);
	}

	public function benchIndexOfFirst(): int
	{
		$probe = $this->probe;
		return $this->collection->indexOfFirst(static fn (int $v): bool => $v === $probe);
	}

	public function benchIndexOfLast(): int
	{
		$probe = $this->probe;
		return $this->collection->indexOfLast(static fn (int $v): bool => $v === $probe);
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchSlice(): ImmutableList
	{
		return $this->collection->slice($this->half >> 1, $this->half + ($this->half >> 1));
	}

	/**
	 * @return ImmutableMap<int, int>
	 */
	public function benchToIndexedMap(): ImmutableMap
	{
		return $this->collection->toIndexedMap();
	}
}
