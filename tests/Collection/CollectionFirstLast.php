<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection;

use Noctud\Collection\Exception\NoSuchElementException;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

trait CollectionFirstLast
{
	#[Test]
	public function first_last_key_value(): void
	{
		$object = new stdClass();
		$collection = $this->collectionOf(['a', 'b', 'c', $object]);

		$this->assertSame(['a', 'a'], [$collection->first(), $collection->firstOrNull()]);
		$this->assertSame([$object, $object], [$collection->last(), $collection->lastOrNull()]);
	}

	#[Test]
	public function first_last_key_value_on_empty_collection(): void
	{
		$collection = $this->collectionOf([]);

		try {
			$collection->first();
			$this->assertFalse(true, 'Expected NoSuchElementException not thrown for firstKey'); /** @phpstan-ignore-line **/
		} catch (NoSuchElementException) {
			// Expected exception, test passes
		}

		try {
			$collection->last();
			$this->assertFalse(true, 'Expected NoSuchElementException not thrown for lastKey'); /** @phpstan-ignore-line **/
		} catch (NoSuchElementException) {
			// Expected exception, test passes
		}

		$this->assertSame([null, null], [$collection->firstOrNull(), $collection->lastOrNull()]);
	}

	#[Test]
	public function find_returns_first_match(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5]);

		$this->assertSame(2, $collection->find(fn ($v) => $v % 2 === 0));
	}

	#[Test]
	public function find_returns_null_when_no_match(): void
	{
		$collection = $this->collectionOf([1, 3, 5]);

		$this->assertNull($collection->find(fn ($v) => $v % 2 === 0));
	}

	#[Test]
	public function find_returns_null_on_empty_collection(): void
	{
		$collection = $this->collectionOf([]);

		$this->assertNull($collection->find(fn ($v) => true));
	}

	#[Test]
	public function find_has_early_termination(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5]);
		$count = 0;

		$collection->find(function ($v) use (&$count) {
			$count++;
			return $v === 2;
		});

		$this->assertSame(2, $count);
	}

	#[Test]
	public function findLast_returns_last_match(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5]);

		$this->assertSame(4, $collection->findLast(fn ($v) => $v % 2 === 0));
	}

	#[Test]
	public function findLast_returns_null_when_no_match(): void
	{
		$collection = $this->collectionOf([1, 3, 5]);

		$this->assertNull($collection->findLast(fn ($v) => $v % 2 === 0));
	}

	#[Test]
	public function findLast_returns_null_on_empty_collection(): void
	{
		$collection = $this->collectionOf([]);

		$this->assertNull($collection->findLast(fn ($v) => true));
	}

	#[Test]
	public function findLast_iterates_all_elements(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5]);
		$count = 0;

		$collection->findLast(function ($v) use (&$count) {
			$count++;
			return $v === 2;
		});

		$this->assertSame(5, $count);
	}

	#[Test]
	public function expect_returns_first_match(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5]);

		$this->assertSame(2, $collection->expect(fn ($v) => $v % 2 === 0));
	}

	#[Test]
	public function expect_throws_when_no_match(): void
	{
		$collection = $this->collectionOf([1, 3, 5]);

		$this->expectException(NoSuchElementException::class);
		$collection->expect(fn ($v) => $v % 2 === 0);
	}

	#[Test]
	public function expect_throws_on_empty_collection(): void
	{
		$collection = $this->collectionOf([]);

		$this->expectException(NoSuchElementException::class);
		$collection->expect(fn ($v) => true);
	}

	#[Test]
	public function expectLast_returns_last_match(): void
	{
		$collection = $this->collectionOf([1, 2, 3, 4, 5]);

		$this->assertSame(4, $collection->expectLast(fn ($v) => $v % 2 === 0));
	}

	#[Test]
	public function expectLast_throws_when_no_match(): void
	{
		$collection = $this->collectionOf([1, 3, 5]);

		$this->expectException(NoSuchElementException::class);
		$collection->expectLast(fn ($v) => $v % 2 === 0);
	}

	#[Test]
	public function expectLast_throws_on_empty_collection(): void
	{
		$collection = $this->collectionOf([]);

		$this->expectException(NoSuchElementException::class);
		$collection->expectLast(fn ($v) => true);
	}
}
