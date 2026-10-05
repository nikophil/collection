<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Collection;

use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use Noctud\Collection\Collection;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;

/**
 * Base of the List and Set benchmarks: every subject runs against a collection of unique
 * integers in a shuffled order, for each size of ProvidesSizes.
 *
 * @template C of Collection<int>
 */
#[BeforeMethods('setUp')]
#[ParamProviders('provideSizes')]
abstract class AbstractCollectionBenchCase
{
	use ProvidesSizes;

	/** Revolutions for subjects of a few microseconds at most, too fast to be measured reliably with the default. */
	protected const int FastSubjectRevs = 1000;

	/** Number of calls made by subjects measuring a cheap per-element operation. */
	protected const int Batch = 100;

	/** @var C */
	protected Collection $collection;

	/** @var list<int> */
	protected array $elements;

	/** @var list<int> Half of it overlaps $elements, the other half does not. */
	protected array $other;

	/** An element located in the middle of the collection. */
	protected int $probe;

	/** @var int<0, max> Half of the collection size. */
	protected int $half;

	/**
	 * @param list<int> $elements
	 * @return C
	 */
	abstract protected function collectionOf(array $elements): Collection;

	/**
	 * @param array{size: positive-int} $params
	 */
	public function setUp(array $params): void
	{
		$size = $params['size'];
		$this->half = $size >> 1;
		$this->elements = Data::ints($size);
		$this->other = Data::ints($size, $this->half);
		$this->probe = $this->elements[$this->half];
		$this->collection = $this->collectionOf($this->elements);
	}
}
