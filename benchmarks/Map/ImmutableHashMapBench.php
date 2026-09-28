<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\ImmutableMap;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\mapOf;

/**
 * Writes on an immutable HashMap, with string keys, each returning a modified copy.
 *
 * @extends AbstractMapBenchCase<string, ImmutableMap<string, int>>
 */
#[Groups(['map', 'mutation'])]
final class ImmutableHashMapBench extends AbstractMapBenchCase
{
	use MapImmutableWrite;
	use StringKeys;

	protected function mapOf(array $entries): ImmutableMap
	{
		return mapOf($entries);
	}
}
