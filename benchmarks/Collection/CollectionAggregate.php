<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Map\ImmutableMap;

/**
 * Reductions over the whole collection.
 */
trait CollectionAggregate
{
	public function benchFold(): int
	{
		return $this->collection->fold(0, static fn (int $acc, int $v): int => $acc + $v);
	}

	public function benchReduce(): int
	{
		return $this->collection->reduce(static fn (int $acc, int $v): int => $acc + $v);
	}

	public function benchReduceOrNull(): ?int
	{
		return $this->collection->reduceOrNull(static fn (int $acc, int $v): int => $acc + $v);
	}

	public function benchSum(): int|float
	{
		return $this->collection->sum();
	}

	public function benchSumWithSelector(): int|float
	{
		return $this->collection->sum(static fn (int $v): int => $v * 2);
	}

	public function benchAvg(): float
	{
		return $this->collection->avg();
	}

	public function benchAvgOrNull(): ?float
	{
		return $this->collection->avgOrNull();
	}

	public function benchMin(): int
	{
		return $this->collection->min();
	}

	public function benchMinOrNull(): ?int
	{
		return $this->collection->minOrNull();
	}

	public function benchMinWithSelector(): int
	{
		return $this->collection->min(static fn (int $v): int => -$v);
	}

	public function benchMax(): int
	{
		return $this->collection->max();
	}

	public function benchMaxOrNull(): ?int
	{
		return $this->collection->maxOrNull();
	}

	public function benchMaxWithSelector(): int
	{
		return $this->collection->max(static fn (int $v): int => -$v);
	}

	public function benchMinOf(): int
	{
		return $this->collection->minOf(static fn (int $v): int => -$v);
	}

	public function benchMinOfOrNull(): ?int
	{
		return $this->collection->minOfOrNull(static fn (int $v): int => -$v);
	}

	public function benchMaxOf(): int
	{
		return $this->collection->maxOf(static fn (int $v): int => -$v);
	}

	public function benchMaxOfOrNull(): ?int
	{
		return $this->collection->maxOfOrNull(static fn (int $v): int => -$v);
	}

	public function benchJoinToString(): string
	{
		return $this->collection->joinToString();
	}

	public function benchJoinToStringWithTransform(): string
	{
		return $this->collection->joinToString(transform: static fn (int $v): string => "#$v");
	}

	/**
	 * @return ImmutableMap<bool, int>
	 */
	public function benchCountBy(): ImmutableMap
	{
		return $this->collection->countBy(static fn (int $v): bool => $v % 2 === 0);
	}
}
