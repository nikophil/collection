<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\MutableMap;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\mutableStringMapOf;

/**
 * In-place mutations of a mutable StringMap.
 * Its tracked() view wraps the same MutableTrackedHashMap as any map: MutableHashMapBench measures it.
 *
 * @extends AbstractMapBenchCase<string, MutableMap<string, int>>
 */
#[Groups(['map', 'mutation'])]
final class MutableStringMapBench extends AbstractMapBenchCase
{
	use MapMutate;
	use StringKeys;

	protected function mapOf(array $entries): MutableMap
	{
		return mutableStringMapOf($entries);
	}

	/**
	 * @return MutableMap<string, int>
	 */
	protected function mutable(): MutableMap
	{
		return mutableStringMapOf($this->map);
	}
}
