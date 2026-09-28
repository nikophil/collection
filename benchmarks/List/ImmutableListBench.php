<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\Benchmarks\Collection\AbstractCollectionBenchCase;
use Noctud\Collection\Benchmarks\Collection\CollectionImmutableWrite;
use Noctud\Collection\List\ImmutableList;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\listOf;

/**
 * Writes on an immutable List, each returning a modified copy.
 *
 * @extends AbstractCollectionBenchCase<ImmutableList<int>>
 */
#[Groups(['list', 'mutation'])]
final class ImmutableListBench extends AbstractCollectionBenchCase
{
	use CollectionImmutableWrite;
	use ListImmutableWrite;

	protected function collectionOf(array $elements): ImmutableList
	{
		return listOf($elements);
	}
}
