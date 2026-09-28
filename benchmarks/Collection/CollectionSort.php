<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Collection;

/**
 * Sorting a collection given in a shuffled order.
 */
trait CollectionSort
{
	/**
	 * @return Collection<int>
	 */
	public function benchSorted(): Collection
	{
		return $this->collection->sorted();
	}

	/**
	 * @return Collection<int>
	 */
	public function benchSortedDesc(): Collection
	{
		return $this->collection->sortedDesc();
	}

	/**
	 * @return Collection<int>
	 */
	public function benchSortedBy(): Collection
	{
		return $this->collection->sortedBy(static fn (int $v): int => -$v);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchSortedByDesc(): Collection
	{
		return $this->collection->sortedByDesc(static fn (int $v): int => -$v);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchSortedWith(): Collection
	{
		return $this->collection->sortedWith(static fn (int $a, int $b): int => $a <=> $b);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchReversed(): Collection
	{
		return $this->collection->reversed();
	}

	/**
	 * @return Collection<int>
	 */
	public function benchShuffled(): Collection
	{
		return $this->collection->shuffled();
	}
}
