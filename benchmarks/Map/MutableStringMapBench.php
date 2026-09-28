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
use function Noctud\Collection\mutableStringMapOf;

/**
 * In-place mutations of a mutable StringMap, directly and through its tracked() view.
 *
 * @extends AbstractMapBenchCase<string, MutableMap<string, int>>
 */
#[Groups(['map', 'mutation'])]
#[BeforeMethods('setUpTracking')]
#[ParamProviders('provideTracking')]
final class MutableStringMapBench extends AbstractMapBenchCase
{
	use MapMutate;
	use ProvidesTracking;
	use StringKeys;

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
		return mutableStringMapOf($entries);
	}

	/**
	 * @return MutableMap<string, int>
	 */
	protected function mutable(): MutableMap
	{
		$copy = mutableStringMapOf($this->map);
		return $this->tracked ? $copy->tracked() : $copy;
	}
}
