<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\Benchmarks\Collection\CollectionAccess;
use Noctud\Collection\Benchmarks\Collection\CollectionAggregate;
use Noctud\Collection\Benchmarks\Collection\AbstractCollectionBenchCase;
use Noctud\Collection\Benchmarks\Collection\CollectionQuery;
use Noctud\Collection\List\ImmutableList;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\listOf;

/**
 * Read-only operations of an immutable List.
 *
 * @extends AbstractCollectionBenchCase<ImmutableList<int>>
 */
#[Groups(['list'])]
final class ListBench extends AbstractCollectionBenchCase
{
	use CollectionAccess;
	use CollectionQuery;
	use CollectionAggregate;

	protected function collectionOf(array $elements): ImmutableList
	{
		return listOf($elements);
	}
}
