<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\MutableCollection;
use PhpBench\Attributes\Revs;

/**
 * In-place mutations. Each subject works on its own copy, from mutable(): the copy is
 * O(1) until the first write duplicates the underlying array, as `$copy = $array` would.
 * Cheap per-element mutations run over a batch.
 */
trait CollectionMutate
{
	/**
	 * @return MutableCollection<int>
	 */
	abstract protected function mutable(): MutableCollection;

	/**
	 * @return MutableCollection<int>
	 */
	public function benchAdd(): MutableCollection
	{
		$collection = $this->mutable();
		for ($i = 0; $i < self::Batch; $i++) {
			$collection->add(-$i - 1);
		}

		return $collection;
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchAddFirst(): MutableCollection
	{
		$collection = $this->mutable();
		for ($i = 0; $i < self::Batch; $i++) {
			$collection->addFirst(-$i - 1);
		}

		return $collection;
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchAddAll(): MutableCollection
	{
		return $this->mutable()->addAll($this->other);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchRemoveElement(): MutableCollection
	{
		$collection = $this->mutable();
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$collection->removeElement($this->elements[intdiv($i * $count, self::Batch)]);
		}

		return $collection;
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchRemoveIf(): MutableCollection
	{
		return $this->mutable()->removeIf(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchRemoveAll(): MutableCollection
	{
		return $this->mutable()->removeAll($this->other);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchRetainAll(): MutableCollection
	{
		return $this->mutable()->retainAll($this->other);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchRemoveFirst(): MutableCollection
	{
		$collection = $this->mutable();
		for ($i = 0; $i < self::Batch; $i++) {
			$collection->removeFirst();
		}

		return $collection;
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchRemoveLast(): MutableCollection
	{
		$collection = $this->mutable();
		for ($i = 0; $i < self::Batch; $i++) {
			$collection->removeLast();
		}

		return $collection;
	}

	/**
	 * @return MutableCollection<int>
	 */
	#[Revs(self::ConstantTimeRevs)]
	public function benchClear(): MutableCollection
	{
		return $this->mutable()->clear();
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchSort(): MutableCollection
	{
		return $this->mutable()->sort();
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchSortDesc(): MutableCollection
	{
		return $this->mutable()->sortDesc();
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchSortBy(): MutableCollection
	{
		return $this->mutable()->sortBy(static fn (int $v): int => -$v);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchSortByDesc(): MutableCollection
	{
		return $this->mutable()->sortByDesc(static fn (int $v): int => -$v);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchSortWith(): MutableCollection
	{
		return $this->mutable()->sortWith(static fn (int $a, int $b): int => $a <=> $b);
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchReverse(): MutableCollection
	{
		return $this->mutable()->reverse();
	}

	/**
	 * @return MutableCollection<int>
	 */
	public function benchShuffle(): MutableCollection
	{
		return $this->mutable()->shuffle();
	}
}
