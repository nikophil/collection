<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Benchmarks\Fixture\Item;
use Noctud\Collection\Map\Map;

/**
 * Filters keeping about half of the entries.
 */
trait MapFilter
{
	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchFilter(): Map
	{
		return $this->map->filter(static fn (int $v, int|string $k): bool => $v % 2 === 0);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchFilterKeys(): Map
	{
		$probeKey = $this->probeKey;
		return $this->map->filterKeys(static fn (int|string $k): bool => $k < $probeKey);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchFilterValues(): Map
	{
		return $this->map->filterValues(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchFilterValuesNotNull(): Map
	{
		return $this->map->filterValuesNotNull();
	}

	/**
	 * @return Map<covariant int|string, Item>
	 */
	public function benchFilterValuesInstanceOf(): Map
	{
		return $this->map->filterValuesInstanceOf(Item::class);
	}
}
