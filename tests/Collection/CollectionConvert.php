<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection;

use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Set\ImmutableSet;
use PHPUnit\Framework\Attributes\Test;

trait CollectionConvert
{
	#[Test]
	public function toArray_returns_indexed_array(): void
	{
		$collection = $this->collectionOf(['a', 'b', 'c']);
		$this->assertSame(['a', 'b', 'c'], $collection->toArray());
	}

	#[Test]
	public function toArray_on_empty(): void
	{
		$collection = $this->collectionOf([]);
		$this->assertSame([], $collection->toArray());
	}

	#[Test]
	public function jsonSerialize_matches_toArray(): void
	{
		$collection = $this->collectionOf([1, 2, 3]);
		$this->assertSame($collection->toArray(), $collection->jsonSerialize());
	}

	#[Test]
	public function jsonSerialize_on_empty(): void
	{
		$collection = $this->collectionOf([]);
		$this->assertSame([], $collection->jsonSerialize());
	}

	#[Test]
	public function count_returns_total_elements(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5, 6]);
		$this->assertSame(6, $collection->count());
	}

	#[Test]
	public function count_on_empty(): void
	{
		$collection = $this->collectionOf([]);
		$this->assertSame(0, $collection->count());
	}

	#[Test]
	public function toList_preserves_order(): void
	{
		$collection = $this->collectionOf([3, 1, 2]);
		$list = $collection->toList();

		$this->assertSame([3, 1, 2], $list->toArray());
		$this->assertInstanceOf(ImmutableList::class, $list);
	}

	#[Test]
	public function toList_on_empty(): void
	{
		$collection = $this->collectionOf([]);
		$this->assertSame([], $collection->toList()->toArray());
	}

	#[Test]
	public function toSet_removes_duplicates(): void
	{
		$collection = $this->collectionOf([1, 2, 2, 3, 1]);
		$set = $collection->toSet();

		$this->assertSame([1, 2, 3], $set->toArray());
		$this->assertInstanceOf(ImmutableSet::class, $set);
	}

	#[Test]
	public function toSet_on_empty(): void
	{
		$collection = $this->collectionOf([]);
		$this->assertSame([], $collection->toSet()->toArray());
	}

	#[Test]
	public function debug_info(): void
	{
		$collection = $this->collectionOf(['a', 'b', 'c']);

		$debugInfo = $collection->__debugInfo(); /** @phpstan-ignore-line */
		$this->assertSame(['a', 'b', 'c'], $debugInfo);
	}
}
