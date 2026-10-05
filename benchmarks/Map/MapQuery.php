<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

/**
 * Predicates evaluated against the entries.
 */
trait MapQuery
{
	public function benchAll(): bool
	{
		return $this->map->all(static fn (int $v, int|string $k): bool => $v >= 0);
	}

	public function benchAny(): bool
	{
		return $this->map->any(static fn (int $v, int|string $k): bool => $v < 0);
	}

	public function benchNone(): bool
	{
		return $this->map->none(static fn (int $v, int|string $k): bool => $v < 0);
	}

	public function benchCountWhere(): int
	{
		return $this->map->countWhere(static fn (int $v, int|string $k): bool => $v % 2 === 0);
	}
}
