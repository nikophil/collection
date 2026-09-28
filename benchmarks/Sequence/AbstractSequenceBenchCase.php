<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Sequence;

use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use Noctud\Collection\Sequence\Sequence;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;
use function Noctud\Collection\sequenceOf;

/**
 * Base of the Sequence benchmarks: every subject runs against a sequence over an array of
 * unique integers in a shuffled order, for each size of ProvidesSizes.
 */
#[BeforeMethods('setUp')]
#[ParamProviders('provideSizes')]
abstract class AbstractSequenceBenchCase
{
	use ProvidesSizes;

	/** Revolutions for constant-time subjects, too fast to be measured reliably with the default. */
	protected const int ConstantTimeRevs = 1000;

	/** @var Sequence<int> */
	protected Sequence $sequence;

	/** @var list<int> */
	protected array $elements;

	/** @var list<int> Half of it overlaps $elements, the other half does not. */
	protected array $other;

	/** An element located in the middle of the sequence. */
	protected int $probe;

	/** @var int<0, max> Half of the sequence size. */
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
		$this->sequence = sequenceOf($this->elements);
	}
}
