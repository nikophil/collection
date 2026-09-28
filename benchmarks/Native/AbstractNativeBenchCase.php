<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Native;

use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;

/**
 * Base of the native array references, on the same data as the collection benchmarks.
 * Subjects share the name of the collection subject they are the counterpart of.
 */
#[Groups(['native'])]
#[BeforeMethods('setUp')]
#[ParamProviders('provideSizes')]
abstract class AbstractNativeBenchCase
{
	use ProvidesSizes;

	/** Revolutions for constant-time subjects, too fast to be measured reliably with the default. */
	protected const int ConstantTimeRevs = 1000;

	protected const int Batch = 100;

	/** @var non-empty-list<int> */
	protected array $elements;

	/** @var list<int> Half of it overlaps $elements, the other half does not. */
	protected array $other;

	/** An element located in the middle of $elements. */
	protected int $probe;

	/** @var int<0, max> */
	protected int $half;

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
	}
}
