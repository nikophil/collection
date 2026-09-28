<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Set;

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
use Noctud\Collection\Set\ImmutableSet;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\setOf;

/**
 * Read-only operations of an immutable Set.
 *
 * @extends AbstractCollectionBenchCase<ImmutableSet<int>>
 */
#[Groups(['set'])]
final class SetBench extends AbstractCollectionBenchCase
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

	protected function collectionOf(array $elements): ImmutableSet
	{
		return setOf($elements);
	}
}
