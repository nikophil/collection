<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\List\MutableList;

/**
 * Index-based in-place mutations, on top of CollectionMutate.
 */
trait ListMutate
{
	/**
	 * @return MutableList<int>
	 */
	public function benchSet(): MutableList
	{
		$list = $this->mutable();
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$list->set(intdiv($i * $count, self::Batch), -$i);
		}

		return $list;
	}

	/**
	 * @return MutableList<int>
	 */
	public function benchOffsetSet(): MutableList
	{
		$list = $this->mutable();
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$list[intdiv($i * $count, self::Batch)] = -$i;
		}

		return $list;
	}

	/**
	 * Removes from the middle of the list, the worst case of a reindexing removal.
	 *
	 * @return MutableList<int>
	 */
	public function benchRemoveAt(): MutableList
	{
		$list = $this->mutable();
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$list->removeAt(($count - $i) >> 1);
		}

		return $list;
	}

	/**
	 * @return MutableList<int>
	 */
	public function benchRemoveEvery(): MutableList
	{
		return $this->mutable()->removeEvery($this->probe);
	}
}
