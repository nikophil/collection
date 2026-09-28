<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Benchmarks\Fixture\Item;
use Noctud\Collection\Collection;

/**
 * Filters keeping about half of the elements.
 */
trait CollectionFilter
{
	/**
	 * @return Collection<int>
	 */
	public function benchFilter(): Collection
	{
		return $this->collection->filter(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchFilterNotNull(): Collection
	{
		return $this->collection->filterNotNull();
	}

	/**
	 * @return Collection<Item>
	 */
	public function benchFilterInstanceOf(): Collection
	{
		return $this->collection->filterInstanceOf(Item::class);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchDistinct(): Collection
	{
		return $this->collection->distinct();
	}

	/**
	 * @return Collection<int>
	 */
	public function benchDistinctBy(): Collection
	{
		return $this->collection->distinctBy(static fn (int $v): int => $v % 1000);
	}
}
