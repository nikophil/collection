<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Benchmarks\Fixture\ProvidesTracking;
use Noctud\Collection\Map\MutableMap;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use function Noctud\Collection\mutableIntMapOf;

/**
 * In-place mutations of a mutable IntMap, directly and through its tracked() view.
 *
 * @extends AbstractMapBenchCase<int, MutableMap<int, int>>
 */
#[Groups(['map', 'mutation'])]
#[BeforeMethods('setUpTracking')]
#[ParamProviders('provideTracking')]
final class MutableIntMapBench extends AbstractMapBenchCase
{
	use MapMutate;
	use ProvidesTracking;
	use IntKeys;

	private bool $tracked;

	/**
	 * @param array{tracked: bool} $params
	 */
	public function setUpTracking(array $params): void
	{
		$this->tracked = $params['tracked'];
	}

	protected function mapOf(array $entries): MutableMap
	{
		return mutableIntMapOf($entries);
	}

	/**
	 * @return MutableMap<int, int>
	 */
	protected function mutable(): MutableMap
	{
		$copy = mutableIntMapOf($this->map);
		return $this->tracked ? $copy->tracked() : $copy;
	}
}
