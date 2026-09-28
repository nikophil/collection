<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Collection;

/**
 * Taking or dropping half of the collection, by count or by predicate.
 */
trait CollectionSlice
{
	/**
	 * @return Collection<int>
	 */
	public function benchTakeFirst(): Collection
	{
		return $this->collection->takeFirst($this->half);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchDropFirst(): Collection
	{
		return $this->collection->dropFirst($this->half);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchTakeLast(): Collection
	{
		return $this->collection->takeLast($this->half);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchDropLast(): Collection
	{
		return $this->collection->dropLast($this->half);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchTakeWhile(): Collection
	{
		$probe = $this->probe;
		return $this->collection->takeWhile(static fn (int $v): bool => $v !== $probe);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchDropWhile(): Collection
	{
		$probe = $this->probe;
		return $this->collection->dropWhile(static fn (int $v): bool => $v !== $probe);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchTakeLastWhile(): Collection
	{
		$probe = $this->probe;
		return $this->collection->takeLastWhile(static fn (int $v): bool => $v !== $probe);
	}

	/**
	 * @return Collection<int>
	 */
	public function benchDropLastWhile(): Collection
	{
		$probe = $this->probe;
		return $this->collection->dropLastWhile(static fn (int $v): bool => $v !== $probe);
	}
}
