<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Collection;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Revs;

/**
 * Element lookups. Predicates target the element in the middle of the collection,
 * so find() and friends scan half of it on average.
 */
trait CollectionAccess
{
	/** @var Collection<int> */
	private Collection $singleton;

	public function setUpSingleton(): void
	{
		$this->singleton = $this->collectionOf([$this->probe]);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchFirst(): int
	{
		return $this->collection->first();
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchFirstOrNull(): ?int
	{
		return $this->collection->firstOrNull();
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchLast(): int
	{
		return $this->collection->last();
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchLastOrNull(): ?int
	{
		return $this->collection->lastOrNull();
	}

	#[BeforeMethods('setUpSingleton')]
	#[Revs(self::FastSubjectRevs)]
	public function benchSingle(): int
	{
		return $this->singleton->single();
	}

	#[BeforeMethods('setUpSingleton')]
	#[Revs(self::FastSubjectRevs)]
	public function benchSingleOrNull(): ?int
	{
		return $this->singleton->singleOrNull();
	}

	public function benchFind(): ?int
	{
		$probe = $this->probe;
		return $this->collection->find(static fn (int $v): bool => $v === $probe);
	}

	public function benchFindLast(): ?int
	{
		$probe = $this->probe;
		return $this->collection->findLast(static fn (int $v): bool => $v === $probe);
	}

	public function benchExpect(): int
	{
		$probe = $this->probe;
		return $this->collection->expect(static fn (int $v): bool => $v === $probe);
	}

	public function benchExpectLast(): int
	{
		$probe = $this->probe;
		return $this->collection->expectLast(static fn (int $v): bool => $v === $probe);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchRandom(): int
	{
		return $this->collection->random();
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchRandomOrNull(): ?int
	{
		return $this->collection->randomOrNull();
	}
}
