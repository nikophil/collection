<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Collection;
use Noctud\Collection\List\ListInterface;
use Noctud\Collection\Map\ImmutableMap;
use PhpBench\Attributes\BeforeMethods;

/**
 * Operations splitting the collection into several parts or combining it with another one.
 * unzip() reuses the pairs built by CollectionTransform::setUpPairs().
 */
trait CollectionGroupAndZip
{
	/**
	 * @return ListInterface<ListInterface<int>>
	 */
	public function benchChunked(): ListInterface
	{
		return $this->collection->chunked(10);
	}

	/**
	 * @return ListInterface<ListInterface<int>>
	 */
	public function benchWindowed(): ListInterface
	{
		return $this->collection->windowed(10, 5);
	}

	/**
	 * @return ListInterface<array{int, int}>
	 */
	public function benchZip(): ListInterface
	{
		return $this->collection->zip($this->other);
	}

	/**
	 * @return ListInterface<array{int, int}>
	 */
	public function benchZipWithNext(): ListInterface
	{
		return $this->collection->zipWithNext();
	}

	/**
	 * @return array{ListInterface<mixed>, ListInterface<mixed>}
	 */
	#[BeforeMethods('setUpPairs')]
	public function benchUnzip(): array
	{
		return $this->pairs->unzip();
	}

	/**
	 * @return array{Collection<int>, Collection<int>}
	 */
	public function benchPartition(): array
	{
		return $this->collection->partition(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return ImmutableMap<bool, covariant Collection<int>>
	 */
	public function benchGroupBy(): ImmutableMap
	{
		return $this->collection->groupBy(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return ImmutableMap<bool, covariant Collection<int>>
	 */
	public function benchGroupByWithValueTransform(): ImmutableMap
	{
		return $this->collection->groupBy(static fn (int $v): bool => $v % 2 === 0, static fn (int $v): int => $v * 2);
	}
}
