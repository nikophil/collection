<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\ImmutableMap;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\stringMapOf;

/**
 * Writes on an immutable StringMap, each returning a modified copy.
 *
 * @extends AbstractMapBenchCase<string, ImmutableMap<string, int>>
 */
#[Groups(['map', 'mutation', 'guard'])]
final class ImmutableStringMapBench extends AbstractMapBenchCase
{
	use MapImmutableWrite;
	use StringKeys;

	protected function mapOf(array $entries): ImmutableMap
	{
		return stringMapOf($entries);
	}
}
