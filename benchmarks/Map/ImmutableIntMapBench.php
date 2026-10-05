<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\ImmutableMap;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\intMapOf;

/**
 * Writes on an immutable IntMap, each returning a modified copy.
 *
 * @extends AbstractMapBenchCase<int, ImmutableMap<int, int>>
 */
#[Groups(['map', 'mutation', 'guard'])]
final class ImmutableIntMapBench extends AbstractMapBenchCase
{
	use MapImmutableWrite;
	use IntKeys;

	protected function mapOf(array $entries): ImmutableMap
	{
		return intMapOf($entries);
	}
}
