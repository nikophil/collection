<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Set;

use Noctud\Collection\Benchmarks\Collection\AbstractCollectionBenchCase;
use Noctud\Collection\Benchmarks\Collection\CollectionMutate;
use Noctud\Collection\Benchmarks\Fixture\ProvidesTracking;
use Noctud\Collection\Set\MutableSet;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use function Noctud\Collection\mutableSetOf;

/**
 * In-place mutations of a mutable Set, directly and through its tracked() view.
 *
 * @extends AbstractCollectionBenchCase<MutableSet<int>>
 */
#[Groups(['set', 'mutation', 'guard'])]
#[BeforeMethods('setUpTracking')]
#[ParamProviders('provideTracking')]
final class MutableSetBench extends AbstractCollectionBenchCase
{
	use CollectionMutate;
	use ProvidesTracking;

	private bool $tracked;

	/**
	 * @param array{tracked: bool} $params
	 */
	public function setUpTracking(array $params): void
	{
		$this->tracked = $params['tracked'];
	}

	protected function collectionOf(array $elements): MutableSet
	{
		return mutableSetOf($elements);
	}

	/**
	 * @return MutableSet<int>
	 */
	protected function mutable(): MutableSet
	{
		$copy = mutableSetOf($this->collection);
		return $this->tracked ? $copy->tracked() : $copy;
	}
}
