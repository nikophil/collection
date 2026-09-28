<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Collection;
use PhpBench\Attributes\BeforeMethods;

trait CollectionTransform
{
	/** @var Collection<array{int, int}> */
	private Collection $pairs;

	public function setUpPairs(): void
	{
		$this->pairs = $this->collection->map(static fn (int $v): array => [$v, -$v]);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchMap(): Collection
	{
		return $this->collection->map(static fn (int $v): int => $v * 2);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchMapNotNull(): Collection
	{
		return $this->collection->mapNotNull(static fn (int $v): ?int => $v % 2 === 0 ? $v : null);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchFlatMap(): Collection
	{
		return $this->collection->flatMap(static fn (int $v): array => [$v, -$v]);
	}

	/**
	 * @return Collection<int>
	 */
	#[BeforeMethods('setUpPairs')]
	public function benchFlatten(): Collection
	{
		return $this->pairs->flatten();
	}
}
