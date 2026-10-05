<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\MutableMap;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\mutableIntMapOf;

/**
 * In-place mutations of a mutable IntMap.
 * Its tracked() view wraps the same MutableTrackedHashMap as any map: MutableHashMapBench measures it.
 *
 * @extends AbstractMapBenchCase<int, MutableMap<int, int>>
 */
#[Groups(['map', 'mutation'])]
final class MutableIntMapBench extends AbstractMapBenchCase
{
	use MapMutate;
	use IntKeys;

	protected function mapOf(array $entries): MutableMap
	{
		return mutableIntMapOf($entries);
	}

	/**
	 * @return MutableMap<int, int>
	 */
	protected function mutable(): MutableMap
	{
		return mutableIntMapOf($this->map);
	}
}
