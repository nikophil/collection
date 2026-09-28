<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use PhpBench\Attributes\Revs;

/**
 * Checks answering a boolean or a count. contains() is O(1) on a Set and O(n) on a List,
 * so it is measured over a batch of lookups spread over the whole collection.
 */
trait CollectionQuery
{
	#[Revs(self::ConstantTimeRevs)]
	public function benchIsEmpty(): bool
	{
		return $this->collection->isEmpty();
	}

	#[Revs(self::ConstantTimeRevs)]
	public function benchIsNotEmpty(): bool
	{
		return $this->collection->isNotEmpty();
	}

	#[Revs(self::ConstantTimeRevs)]
	public function benchCount(): int
	{
		return $this->collection->count();
	}

	public function benchContains(): int
	{
		$found = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) $this->collection->contains($this->elements[intdiv($i * $count, self::Batch)]);
		}

		return $found;
	}

	public function benchContainsAll(): bool
	{
		return $this->collection->containsAll($this->elements);
	}

	public function benchAll(): bool
	{
		return $this->collection->all(static fn (int $v): bool => $v >= 0);
	}

	public function benchAny(): bool
	{
		return $this->collection->any(static fn (int $v): bool => $v < 0);
	}

	public function benchNone(): bool
	{
		return $this->collection->none(static fn (int $v): bool => $v < 0);
	}

	public function benchCountWhere(): int
	{
		return $this->collection->countWhere(static fn (int $v): bool => $v % 2 === 0);
	}
}
