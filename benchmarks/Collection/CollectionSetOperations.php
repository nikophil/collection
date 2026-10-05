<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Set\Set;

/**
 * Set algebra against an iterable overlapping half of the collection.
 */
trait CollectionSetOperations
{
	/**
	 * @return Set<int>
	 */
	public function benchIntersect(): Set
	{
		return $this->collection->intersect($this->other);
	}

	/**
	 * @return Set<int>
	 */
	public function benchUnion(): Set
	{
		return $this->collection->union($this->other);
	}

	/**
	 * @return Set<int>
	 */
	public function benchSubtract(): Set
	{
		return $this->collection->subtract($this->other);
	}
}
