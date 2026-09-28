<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Sequence;

use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\Sequence\Sequence;
use Noctud\Collection\Set\ImmutableSet;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Revs;
use function Noctud\Collection\sequenceOf;

/**
 * Terminal operations, draining the sequence or stopping as soon as the answer is known.
 * Predicates target the element in the middle of the sequence.
 */
#[Groups(['sequence'])]
final class SequenceTerminalBench extends AbstractSequenceBenchCase
{
	/** @var Sequence<int> */
	private Sequence $singleton;

	public function setUpSingleton(): void
	{
		$this->singleton = sequenceOf([$this->probe]);
	}

	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->sequence as $v) {
			$sum += $v;
		}

		return $sum;
	}

	public function benchForEach(): int
	{
		$sum = 0;
		$this->sequence->forEach(static function (int $v) use (&$sum): void {
			$sum += $v;
		});

		return $sum;
	}

	#[Revs(self::ConstantTimeRevs)]
	public function benchFirst(): int
	{
		return $this->sequence->first();
	}

	#[Revs(self::ConstantTimeRevs)]
	public function benchFirstOrNull(): ?int
	{
		return $this->sequence->firstOrNull();
	}

	public function benchLast(): int
	{
		return $this->sequence->last();
	}

	public function benchLastOrNull(): ?int
	{
		return $this->sequence->lastOrNull();
	}

	#[BeforeMethods('setUpSingleton')]
	#[Revs(self::ConstantTimeRevs)]
	public function benchSingle(): int
	{
		return $this->singleton->single();
	}

	#[BeforeMethods('setUpSingleton')]
	#[Revs(self::ConstantTimeRevs)]
	public function benchSingleOrNull(): ?int
	{
		return $this->singleton->singleOrNull();
	}

	public function benchElementAt(): int
	{
		return $this->sequence->elementAt($this->half);
	}

	public function benchElementAtOrNull(): ?int
	{
		return $this->sequence->elementAtOrNull($this->half);
	}

	public function benchFind(): ?int
	{
		$probe = $this->probe;
		return $this->sequence->find(static fn (int $v): bool => $v === $probe);
	}

	public function benchFindLast(): ?int
	{
		$probe = $this->probe;
		return $this->sequence->findLast(static fn (int $v): bool => $v === $probe);
	}

	public function benchExpect(): int
	{
		$probe = $this->probe;
		return $this->sequence->expect(static fn (int $v): bool => $v === $probe);
	}

	public function benchExpectLast(): int
	{
		$probe = $this->probe;
		return $this->sequence->expectLast(static fn (int $v): bool => $v === $probe);
	}

	#[Revs(self::ConstantTimeRevs)]
	public function benchIsEmpty(): bool
	{
		return $this->sequence->isEmpty();
	}

	#[Revs(self::ConstantTimeRevs)]
	public function benchIsNotEmpty(): bool
	{
		return $this->sequence->isNotEmpty();
	}

	public function benchContains(): bool
	{
		return $this->sequence->contains($this->probe);
	}

	public function benchContainsAll(): bool
	{
		return $this->sequence->containsAll($this->elements);
	}

	public function benchAll(): bool
	{
		return $this->sequence->all(static fn (int $v): bool => $v >= 0);
	}

	public function benchAny(): bool
	{
		return $this->sequence->any(static fn (int $v): bool => $v < 0);
	}

	public function benchNone(): bool
	{
		return $this->sequence->none(static fn (int $v): bool => $v < 0);
	}

	public function benchCount(): int
	{
		return $this->sequence->count();
	}

	public function benchCountWhere(): int
	{
		return $this->sequence->countWhere(static fn (int $v): bool => $v % 2 === 0);
	}

	public function benchFold(): int
	{
		return $this->sequence->fold(0, static fn (int $acc, int $v): int => $acc + $v);
	}

	public function benchReduce(): int
	{
		return $this->sequence->reduce(static fn (int $acc, int $v): int => $acc + $v);
	}

	public function benchReduceOrNull(): ?int
	{
		return $this->sequence->reduceOrNull(static fn (int $acc, int $v): int => $acc + $v);
	}

	public function benchSum(): int
	{
		return $this->sequence->sum();
	}

	public function benchSumWithSelector(): int
	{
		return $this->sequence->sum(static fn (int $v): int => $v * 2);
	}

	public function benchAvg(): float
	{
		return $this->sequence->avg();
	}

	public function benchAvgOrNull(): ?float
	{
		return $this->sequence->avgOrNull();
	}

	public function benchMin(): int
	{
		return $this->sequence->min();
	}

	public function benchMinOrNull(): ?int
	{
		return $this->sequence->minOrNull();
	}

	public function benchMinWithSelector(): int
	{
		return $this->sequence->min(static fn (int $v): int => -$v);
	}

	public function benchMax(): int
	{
		return $this->sequence->max();
	}

	public function benchMaxOrNull(): ?int
	{
		return $this->sequence->maxOrNull();
	}

	public function benchMaxWithSelector(): int
	{
		return $this->sequence->max(static fn (int $v): int => -$v);
	}

	public function benchMinOf(): int
	{
		return $this->sequence->minOf(static fn (int $v): int => -$v);
	}

	public function benchMinOfOrNull(): ?int
	{
		return $this->sequence->minOfOrNull(static fn (int $v): int => -$v);
	}

	public function benchMaxOf(): int
	{
		return $this->sequence->maxOf(static fn (int $v): int => -$v);
	}

	public function benchMaxOfOrNull(): ?int
	{
		return $this->sequence->maxOfOrNull(static fn (int $v): int => -$v);
	}

	public function benchJoinToString(): string
	{
		return $this->sequence->joinToString();
	}

	public function benchJoinToStringWithTransform(): string
	{
		return $this->sequence->joinToString(transform: static fn (int $v): string => "#$v");
	}

	/**
	 * @return list<int>
	 */
	public function benchToArray(): array
	{
		return $this->sequence->toArray();
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchToList(): ImmutableList
	{
		return $this->sequence->toList();
	}

	/**
	 * @return ImmutableSet<int>
	 */
	public function benchToSet(): ImmutableSet
	{
		return $this->sequence->toSet();
	}

	/**
	 * @return ImmutableMap<int, int>
	 */
	public function benchToMap(): ImmutableMap
	{
		return $this->sequence->toMap(static fn (int $v): int => $v);
	}
}
