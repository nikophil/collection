<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\Benchmarks\Collection\AbstractCollectionBenchCase;
use Noctud\Collection\Benchmarks\Collection\CollectionAccess;
use Noctud\Collection\Benchmarks\Collection\CollectionAggregate;
use Noctud\Collection\Benchmarks\Collection\CollectionConvert;
use Noctud\Collection\Benchmarks\Collection\CollectionFilter;
use Noctud\Collection\Benchmarks\Collection\CollectionGroupAndZip;
use Noctud\Collection\Benchmarks\Collection\CollectionQuery;
use Noctud\Collection\Benchmarks\Collection\CollectionSetOperations;
use Noctud\Collection\Benchmarks\Collection\CollectionSlice;
use Noctud\Collection\Benchmarks\Collection\CollectionSort;
use Noctud\Collection\Benchmarks\Collection\CollectionTransform;
use Noctud\Collection\List\ImmutableList;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Revs;
use function Noctud\Collection\listOf;

/**
 * Read-only operations of an immutable List.
 *
 * @extends AbstractCollectionBenchCase<ImmutableList<int>>
 */
#[Groups(['list', 'guard'])]
final class ListBench extends AbstractCollectionBenchCase
{
	use CollectionAccess;
	use CollectionQuery;
	use CollectionAggregate;
	use CollectionFilter;
	use CollectionSlice;
	use CollectionTransform;
	use CollectionGroupAndZip;
	use CollectionSetOperations;
	use CollectionSort;
	use CollectionConvert;
	use ListAccess;

	/**
	 * Constant-time here: an immutable List is its own toList().
	 *
	 * @return ImmutableList<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchToList(): ImmutableList
	{
		return $this->collection->toList();
	}

	protected function collectionOf(array $elements): ImmutableList
	{
		return listOf($elements);
	}
}
