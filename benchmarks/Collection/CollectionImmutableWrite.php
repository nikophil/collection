<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\ImmutableCollection;
use PhpBench\Attributes\Revs;

/**
 * Writes on an immutable collection, each returning a modified copy.
 */
trait CollectionImmutableWrite
{
	/**
	 * @return ImmutableCollection<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchAdd(): ImmutableCollection
	{
		return $this->collection->add(-1);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchAddFirst(): ImmutableCollection
	{
		return $this->collection->addFirst(-1);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchAddAll(): ImmutableCollection
	{
		return $this->collection->addAll($this->other);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveElement(): ImmutableCollection
	{
		return $this->collection->removeElement($this->probe);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	public function benchRemoveIf(): ImmutableCollection
	{
		return $this->collection->removeIf(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	public function benchRemoveAll(): ImmutableCollection
	{
		return $this->collection->removeAll($this->other);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	public function benchRetainAll(): ImmutableCollection
	{
		return $this->collection->retainAll($this->other);
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveFirst(): ImmutableCollection
	{
		return $this->collection->removeFirst();
	}

	/**
	 * @return ImmutableCollection<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveLast(): ImmutableCollection
	{
		return $this->collection->removeLast();
	}
}
