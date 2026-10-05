<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Native;

use PhpBench\Attributes\Revs;

/**
 * An array keyed by the elements, the usual native set, as the reference for SetBench and MutableSetBench.
 */
final class NativeSetBench extends AbstractNativeBenchCase
{
	/** @var array<int, true> */
	private array $set;

	/** @var array<int, true> */
	private array $otherSet;

	/**
	 * @param array{size: positive-int} $params
	 */
	public function setUp(array $params): void
	{
		parent::setUp($params);

		$this->set = array_fill_keys($this->elements, true);
		$this->otherSet = array_fill_keys($this->other, true);
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchSetOf(): array
	{
		return array_fill_keys($this->elements, true);
	}

	public function benchContains(): int
	{
		$found = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) isset($this->set[$this->elements[intdiv($i * $count, self::Batch)]]);
		}

		return $found;
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchAdd(): array
	{
		$set = $this->set;
		for ($i = 0; $i < self::Batch; $i++) {
			$set[-$i - 1] = true;
		}

		return $set;
	}

	/**
	 * @return array<int, true>
	 */
	public function benchRemoveElement(): array
	{
		$set = $this->set;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			unset($set[$this->elements[intdiv($i * $count, self::Batch)]]);
		}

		return $set;
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchIntersect(): array
	{
		return array_intersect_key($this->set, $this->otherSet);
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchUnion(): array
	{
		return $this->set + $this->otherSet;
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchSubtract(): array
	{
		return array_diff_key($this->set, $this->otherSet);
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveAll(): array
	{
		return array_diff_key($this->set, $this->otherSet);
	}

	/**
	 * @return array<int, true>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRetainAll(): array
	{
		return array_intersect_key($this->set, $this->otherSet);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->set as $v => $_) {
			$sum += $v;
		}

		return $sum;
	}
}
