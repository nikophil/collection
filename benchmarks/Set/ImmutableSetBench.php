<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Set;

use Noctud\Collection\Benchmarks\Collection\AbstractCollectionBenchCase;
use Noctud\Collection\Benchmarks\Collection\CollectionImmutableWrite;
use Noctud\Collection\Set\ImmutableSet;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\setOf;

/**
 * Writes on an immutable Set, each returning a modified copy.
 *
 * @extends AbstractCollectionBenchCase<ImmutableSet<int>>
 */
#[Groups(['set', 'mutation'])]
final class ImmutableSetBench extends AbstractCollectionBenchCase
{
	use CollectionImmutableWrite;

	protected function collectionOf(array $elements): ImmutableSet
	{
		return setOf($elements);
	}
}
