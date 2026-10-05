<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Collection;
use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\MutableCollection;
use Noctud\Collection\Set\ImmutableSet;
use PhpBench\Attributes\Revs;

/**
 * Iteration and conversion to other structures.
 */
trait CollectionConvert
{
	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->collection as $v) {
			$sum += $v;
		}

		return $sum;
	}

	public function benchForEach(): int
	{
		$sum = 0;
		$this->collection->forEach(static function (int $v) use (&$sum): void {
			$sum += $v;
		});

		return $sum;
	}

	/**
	 * @return list<int>
	 */
	public function benchAsSequence(): array
	{
		return $this->collection->asSequence()->toArray();
	}

	/**
	 * @return ImmutableMap<int, int>
	 */
	public function benchToMap(): ImmutableMap
	{
		return $this->collection->toMap(static fn (int $v): int => $v);
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchToList(): ImmutableList
	{
		return $this->collection->toList();
	}

	/**
	 * @return ImmutableSet<int>
	 */
	public function benchToSet(): ImmutableSet
	{
		return $this->collection->toSet();
	}

	/**
	 * @return list<int>
	 */
	public function benchToArray(): array
	{
		return $this->collection->toArray();
	}

	/**
	 * @return MutableCollection<int>
	 */
	#[Revs(self::ConstantTimeRevs)]
	public function benchToMutable(): MutableCollection
	{
		return $this->collection->toMutable();
	}

	/**
	 * @return Collection<int>
	 */
	#[Revs(self::ConstantTimeRevs)]
	public function benchToImmutable(): Collection
	{
		return $this->collection->toImmutable();
	}

	public function benchJsonEncode(): string
	{
		return json_encode($this->collection, JSON_THROW_ON_ERROR);
	}
}
