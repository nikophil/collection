<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\List\ImmutableList;

/**
 * Index-based writes on an immutable list, on top of CollectionImmutableWrite.
 */
trait ListImmutableWrite
{
	/**
	 * @return ImmutableList<int>
	 */
	public function benchSet(): ImmutableList
	{
		return $this->collection->set($this->half, -1);
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchRemoveAt(): ImmutableList
	{
		return $this->collection->removeAt($this->half);
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchRemoveEvery(): ImmutableList
	{
		return $this->collection->removeEvery($this->probe);
	}
}
