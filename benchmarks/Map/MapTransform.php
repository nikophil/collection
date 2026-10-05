<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Map\Map;

/**
 * Transforms of keys and values, into another map or into a list.
 */
trait MapTransform
{
	/**
	 * @return Map<covariant string, int>
	 */
	public function benchMapKeys(): Map
	{
		return $this->map->mapKeys(static fn (int $v, int|string $k): string => "k$k");
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchMapValues(): Map
	{
		return $this->map->mapValues(static fn (int $v, int|string $k): int => $v * 2);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchMapValuesNotNull(): Map
	{
		return $this->map->mapValuesNotNull(static fn (int $v, int|string $k): ?int => $v % 2 === 0 ? $v : null);
	}

	/**
	 * @return Map<int, covariant int|string>
	 */
	public function benchFlip(): Map
	{
		return $this->map->flip();
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchMap(): ImmutableList
	{
		return $this->map->map(static fn (int $v, int|string $k): int => $v * 2);
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchMapNotNull(): ImmutableList
	{
		return $this->map->mapNotNull(static fn (int $v, int|string $k): ?int => $v % 2 === 0 ? $v : null);
	}

	/**
	 * @return ImmutableList<covariant int|string>
	 */
	public function benchFlatMap(): ImmutableList
	{
		return $this->map->flatMap(static fn (int $v, int|string $k): array => [$k, $v]);
	}
}
