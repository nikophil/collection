<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use Noctud\Collection\Map\Map;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;

/**
 * Base of the Map benchmarks: every subject runs against a map of unique keys to unique
 * integer values in a shuffled order, for each size of ProvidesSizes.
 *
 * @template K of int|string
 * @template M of Map<K, int>
 */
#[BeforeMethods('setUp')]
#[ParamProviders('provideSizes')]
abstract class AbstractMapBenchCase
{
	use ProvidesSizes;

	/** Revolutions for constant-time subjects, too fast to be measured reliably with the default. */
	protected const int ConstantTimeRevs = 1000;

	/** Number of calls made by subjects measuring a cheap per-entry operation. */
	protected const int Batch = 100;

	/** @var M */
	protected Map $map;

	/** @var array<K, int> */
	protected array $entries;

	/** @var list<K> */
	protected array $keys;

	/** @var array<K, int> Half of its keys overlap $entries, the other half do not. */
	protected array $other;

	/** @var K A key located in the middle of the map. */
	protected int|string $probeKey;

	/** The value of $probeKey. */
	protected int $probeValue;

	/** @var int<0, max> Half of the map size. */
	protected int $half;

	/**
	 * @return list<K>
	 */
	abstract protected function keysOf(int $size, int $offset = 0): array;

	/**
	 * @param array<K, int> $entries
	 * @return M
	 */
	abstract protected function mapOf(array $entries): Map;

	/**
	 * @param array{size: positive-int} $params
	 */
	public function setUp(array $params): void
	{
		$size = $params['size'];
		$this->half = $size >> 1;
		$this->keys = $this->keysOf($size);
		$this->entries = array_combine($this->keys, Data::ints($size));
		$this->other = array_combine($this->keysOf($size, $this->half), Data::ints($size, $size));
		$this->probeKey = $this->keys[$this->half];
		$this->probeValue = $this->entries[$this->probeKey];
		$this->map = $this->mapOf($this->entries);
	}
}
