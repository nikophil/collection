<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\Map;
use Noctud\Collection\Map\MapEntry;

/**
 * Sorting a map given in a shuffled order, by key, by value or by entry.
 */
trait MapSort
{
	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByKey(): Map
	{
		return $this->map->sortedByKey();
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByKeyDesc(): Map
	{
		return $this->map->sortedByKeyDesc();
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByKeyWithSelector(): Map
	{
		return $this->map->sortedByKey(static fn (int|string $k): string => (string) $k);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByValue(): Map
	{
		return $this->map->sortedByValue();
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByValueDesc(): Map
	{
		return $this->map->sortedByValueDesc();
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByValueWithSelector(): Map
	{
		return $this->map->sortedByValue(static fn (int $v): int => -$v);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedBy(): Map
	{
		return $this->map->sortedBy(static fn (int $v, int|string $k): int => -$v);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedByDesc(): Map
	{
		return $this->map->sortedByDesc(static fn (int $v, int|string $k): int => -$v);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedWithKey(): Map
	{
		return $this->map->sortedWithKey(static fn (int|string $a, int|string $b): int => $a <=> $b);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedWithValue(): Map
	{
		return $this->map->sortedWithValue(static fn (int $a, int $b): int => $a <=> $b);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchSortedWith(): Map
	{
		return $this->map->sortedWith(static fn (MapEntry $a, MapEntry $b): int => $a->value <=> $b->value);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchReversed(): Map
	{
		return $this->map->reversed();
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchShuffled(): Map
	{
		return $this->map->shuffled();
	}
}
