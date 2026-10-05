<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Native;

use PhpBench\Attributes\Revs;

/**
 * A plain list array, as the reference for ListBench and MutableListBench.
 */
final class NativeListBench extends AbstractNativeBenchCase
{
	#[Revs(self::FastSubjectRevs)]
	public function benchFirst(): int
	{
		return $this->elements[0];
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchLast(): int
	{
		return $this->elements[count($this->elements) - 1];
	}

	public function benchGet(): int
	{
		$sum = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->elements[intdiv($i * $count, self::Batch)];
		}

		return $sum;
	}

	public function benchContains(): int
	{
		$found = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) in_array($this->elements[intdiv($i * $count, self::Batch)], $this->elements, true);
		}

		return $found;
	}

	public function benchContainsAll(): bool
	{
		return array_diff($this->elements, $this->elements) === [];
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchIndexOf(): int|false
	{
		return array_search($this->probe, $this->elements, true);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchFind(): ?int
	{
		foreach ($this->elements as $v) {
			if ($v === $this->probe) {
				return $v;
			}
		}

		return null;
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchAll(): bool
	{
		foreach ($this->elements as $v) {
			if ($v < 0) {
				return false;
			}
		}

		return true;
	}

	public function benchCountWhere(): int
	{
		return count(array_filter($this->elements, static fn (int $v): bool => $v % 2 === 0));
	}

	public function benchFold(): int
	{
		return array_reduce($this->elements, static fn (int $acc, int $v): int => $acc + $v, 0);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchSum(): int
	{
		return array_sum($this->elements);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchAvg(): float
	{
		return array_sum($this->elements) / count($this->elements);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchMin(): int
	{
		return min($this->elements);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchMax(): int
	{
		return max($this->elements);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchJoinToString(): string
	{
		return implode(', ', $this->elements);
	}

	/**
	 * @return array<int, int>
	 */
	public function benchCountBy(): array
	{
		$counts = [];
		foreach ($this->elements as $v) {
			$key = (int) ($v % 2 === 0);
			$counts[$key] = ($counts[$key] ?? 0) + 1;
		}

		return $counts;
	}

	/**
	 * @return list<int>
	 */
	public function benchFilter(): array
	{
		return array_values(array_filter($this->elements, static fn (int $v): bool => $v % 2 === 0));
	}

	/**
	 * @return list<int>
	 */
	public function benchDistinct(): array
	{
		return array_values(array_unique($this->elements));
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchTakeFirst(): array
	{
		return array_slice($this->elements, 0, $this->half);
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchDropFirst(): array
	{
		return array_slice($this->elements, $this->half);
	}

	/**
	 * @return list<int>
	 */
	public function benchMap(): array
	{
		return array_map(static fn (int $v): int => $v * 2, $this->elements);
	}

	/**
	 * @return list<int>
	 */
	public function benchFlatMap(): array
	{
		return array_merge(...array_map(static fn (int $v): array => [$v, -$v], $this->elements));
	}

	/**
	 * @return list<list<int>>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchChunked(): array
	{
		return array_chunk($this->elements, 10);
	}

	/**
	 * @return list<array{int, int}>
	 */
	public function benchZip(): array
	{
		return array_map(static fn (int $a, int $b): array => [$a, $b], $this->elements, $this->other);
	}

	/**
	 * @return array{list<int>, list<int>}
	 */
	public function benchPartition(): array
	{
		$matching = [];
		$rest = [];
		foreach ($this->elements as $v) {
			if ($v % 2 === 0) {
				$matching[] = $v;
			} else {
				$rest[] = $v;
			}
		}

		return [$matching, $rest];
	}

	/**
	 * @return array<int, list<int>>
	 */
	public function benchGroupBy(): array
	{
		$groups = [];
		foreach ($this->elements as $v) {
			$groups[(int) ($v % 2 === 0)][] = $v;
		}

		return $groups;
	}

	/**
	 * @return list<int>
	 */
	public function benchIntersect(): array
	{
		return array_values(array_intersect($this->elements, $this->other));
	}

	/**
	 * @return list<int>
	 */
	public function benchUnion(): array
	{
		return array_values(array_unique([...$this->elements, ...$this->other]));
	}

	/**
	 * @return list<int>
	 */
	public function benchSubtract(): array
	{
		return array_values(array_diff($this->elements, $this->other));
	}

	/**
	 * @return list<int>
	 */
	public function benchSorted(): array
	{
		$sorted = $this->elements;
		sort($sorted);
		return $sorted;
	}

	/**
	 * @return list<int>
	 */
	public function benchSortedDesc(): array
	{
		$sorted = $this->elements;
		rsort($sorted);
		return $sorted;
	}

	/**
	 * @return list<int>
	 */
	public function benchSortedBy(): array
	{
		$sorted = $this->elements;
		usort($sorted, static fn (int $a, int $b): int => -$a <=> -$b);
		return $sorted;
	}

	/**
	 * @return list<int>
	 */
	public function benchSortedWith(): array
	{
		$sorted = $this->elements;
		usort($sorted, static fn (int $a, int $b): int => $a <=> $b);
		return $sorted;
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchReversed(): array
	{
		return array_reverse($this->elements);
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchShuffled(): array
	{
		$shuffled = $this->elements;
		shuffle($shuffled);
		return $shuffled;
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->elements as $v) {
			$sum += $v;
		}

		return $sum;
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchSlice(): array
	{
		return array_slice($this->elements, $this->half >> 1, $this->half);
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchJsonEncode(): string
	{
		return json_encode($this->elements, JSON_THROW_ON_ERROR);
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchAdd(): array
	{
		$list = $this->elements;
		for ($i = 0; $i < self::Batch; $i++) {
			$list[] = -$i - 1;
		}

		return $list;
	}

	/**
	 * @return list<int>
	 */
	public function benchAddFirst(): array
	{
		$list = $this->elements;
		for ($i = 0; $i < self::Batch; $i++) {
			array_unshift($list, -$i - 1);
		}

		return $list;
	}

	/**
	 * @return array<int, int>
	 */
	public function benchSet(): array
	{
		$list = $this->elements;
		$count = count($list);
		for ($i = 0; $i < self::Batch; $i++) {
			$list[intdiv($i * $count, self::Batch)] = -$i;
		}

		return $list;
	}

	/**
	 * @return list<int>
	 */
	public function benchRemoveFirst(): array
	{
		$list = $this->elements;
		for ($i = 0; $i < self::Batch; $i++) {
			array_shift($list);
		}

		return $list;
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveLast(): array
	{
		$list = $this->elements;
		for ($i = 0; $i < self::Batch; $i++) {
			array_pop($list);
		}

		return $list;
	}

	/**
	 * @return list<int>
	 */
	public function benchRemoveAt(): array
	{
		$list = $this->elements;
		$count = count($list);
		for ($i = 0; $i < self::Batch; $i++) {
			array_splice($list, ($count - $i) >> 1, 1);
		}

		return $list;
	}

	/**
	 * @return list<int>
	 */
	public function benchSort(): array
	{
		$list = $this->elements;
		sort($list);
		return $list;
	}
}
