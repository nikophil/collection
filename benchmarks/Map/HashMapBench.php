<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Map\ImmutableMap;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\mapOf;

/**
 * Read-only operations of the default HashMap, with string keys.
 *
 * @extends AbstractMapBenchCase<string, ImmutableMap<string, int>>
 */
#[Groups(['map'])]
final class HashMapBench extends AbstractMapBenchCase
{
	use MapAccess;
	use MapQuery;
	use MapFilter;
	use MapTransform;
	use MapSlice;
	use MapSort;
	use MapConvert;
	use MapViews;

	protected function keysOf(int $size, int $offset = 0): array
	{
		return Data::strings($size, $offset);
	}

	protected function mapOf(array $entries): ImmutableMap
	{
		return mapOf($entries);
	}
}
