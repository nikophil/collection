<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\MapEntry;
use Noctud\Collection\Map\MutableMap;
use PhpBench\Attributes\Revs;

/**
 * In-place mutations. Each subject works on its own copy, from mutable(): the copy is
 * O(1) until the first write duplicates the underlying arrays, as `$copy = $array` would.
 * Cheap per-entry mutations run over a batch.
 */
trait MapMutate
{
	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	abstract protected function mutable(): MutableMap;

	/**
	 * Inserts keys absent from the map.
	 *
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchPut(): MutableMap
	{
		$map = $this->mutable();
		foreach ($this->newKeys as $i => $key) {
			$map->put($key, $i);
		}

		return $map;
	}

	/**
	 * Replaces the value of existing keys.
	 *
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchOffsetSet(): MutableMap
	{
		$map = $this->mutable();
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$map[$this->keys[intdiv($i * $count, self::Batch)]] = -$i;
		}

		return $map;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchPutFirst(): MutableMap
	{
		$map = $this->mutable();
		foreach ($this->newKeys as $i => $key) {
			$map->putFirst($key, $i);
		}

		return $map;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchPutIfAbsent(): MutableMap
	{
		$map = $this->mutable();
		foreach ($this->newKeys as $i => $key) {
			$map->putIfAbsent($key, $i);
		}

		return $map;
	}

	/**
	 * Computes the value of keys absent from the map.
	 */
	public function benchGetOrPut(): int
	{
		$map = $this->mutable();
		$sum = 0;
		foreach ($this->newKeys as $i => $key) {
			$sum += $map->getOrPut($key, static fn (): int => $i);
		}

		return $sum;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchPutAll(): MutableMap
	{
		return $this->mutable()->putAll($this->other);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchPutAllPairs(): MutableMap
	{
		return $this->mutable()->putAllPairs($this->otherPairs);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemove(): MutableMap
	{
		$map = $this->mutable();
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$map->remove($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $map;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchOffsetUnset(): MutableMap
	{
		$map = $this->mutable();
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			unset($map[$this->keys[intdiv($i * $count, self::Batch)]]);
		}

		return $map;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemoveIf(): MutableMap
	{
		return $this->mutable()->removeIf(static fn (int $v, int|string $k): bool => $v % 2 === 0);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemoveIfKey(): MutableMap
	{
		$probeKey = $this->probeKey;
		return $this->mutable()->removeIfKey(static fn (int|string $k): bool => $k < $probeKey);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemoveIfValue(): MutableMap
	{
		return $this->mutable()->removeIfValue(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemoveNullValues(): MutableMap
	{
		return $this->mutable()->removeNullValues();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemoveFirst(): MutableMap
	{
		$map = $this->mutable();
		for ($i = 0; $i < self::Batch; $i++) {
			$map->removeFirst();
		}

		return $map;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchRemoveLast(): MutableMap
	{
		$map = $this->mutable();
		for ($i = 0; $i < self::Batch; $i++) {
			$map->removeLast();
		}

		return $map;
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	#[Revs(self::ConstantTimeRevs)]
	public function benchClear(): MutableMap
	{
		return $this->mutable()->clear();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortByKey(): MutableMap
	{
		return $this->mutable()->sortByKey();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortByKeyDesc(): MutableMap
	{
		return $this->mutable()->sortByKeyDesc();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortByValue(): MutableMap
	{
		return $this->mutable()->sortByValue();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortByValueDesc(): MutableMap
	{
		return $this->mutable()->sortByValueDesc();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortBy(): MutableMap
	{
		return $this->mutable()->sortBy(static fn (int $v, int|string $k): int => -$v);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortByDesc(): MutableMap
	{
		return $this->mutable()->sortByDesc(static fn (int $v, int|string $k): int => -$v);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortWithKey(): MutableMap
	{
		return $this->mutable()->sortWithKey(static fn (int|string $a, int|string $b): int => $a <=> $b);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortWithValue(): MutableMap
	{
		return $this->mutable()->sortWithValue(static fn (int $a, int $b): int => $a <=> $b);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchSortWith(): MutableMap
	{
		return $this->mutable()->sortWith(static fn (MapEntry $a, MapEntry $b): int => $a->value <=> $b->value);
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchReverse(): MutableMap
	{
		return $this->mutable()->reverse();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	public function benchShuffle(): MutableMap
	{
		return $this->mutable()->shuffle();
	}
}
