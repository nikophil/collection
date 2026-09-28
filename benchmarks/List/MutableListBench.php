<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\List;

use Noctud\Collection\Benchmarks\Collection\AbstractCollectionBenchCase;
use Noctud\Collection\Benchmarks\Collection\CollectionMutate;
use Noctud\Collection\Benchmarks\Fixture\ProvidesTracking;
use Noctud\Collection\List\MutableList;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use function Noctud\Collection\mutableListOf;

/**
 * In-place mutations of a mutable List, directly and through its tracked() view.
 *
 * @extends AbstractCollectionBenchCase<MutableList<int>>
 */
#[Groups(['list', 'mutation'])]
#[BeforeMethods('setUpTracking')]
#[ParamProviders('provideTracking')]
final class MutableListBench extends AbstractCollectionBenchCase
{
	use CollectionMutate;
	use ListMutate;
	use ProvidesTracking;

	private bool $tracked;

	/**
	 * @param array{tracked: bool} $params
	 */
	public function setUpTracking(array $params): void
	{
		$this->tracked = $params['tracked'];
	}

	protected function collectionOf(array $elements): MutableList
	{
		return mutableListOf($elements);
	}

	/**
	 * @return MutableList<int>
	 */
	protected function mutable(): MutableList
	{
		$copy = mutableListOf($this->collection);
		return $this->tracked ? $copy->tracked() : $copy;
	}
}
