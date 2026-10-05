<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Sequence;

use Noctud\Collection\Benchmarks\Fixture\Item;
use Noctud\Collection\Sequence\Sequence;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use function Noctud\Collection\sequenceOf;

/**
 * Lazy intermediate operations. Each subject drains the resulting sequence with toArray(),
 * whose own cost is measured by SequenceTerminalBench::benchToArray().
 */
#[Groups(['sequence', 'guard'])]
final class SequenceIntermediateBench extends AbstractSequenceBenchCase
{
	/** @var Sequence<array{int, int}> */
	private Sequence $pairs;

	public function setUpPairs(): void
	{
		$this->pairs = sequenceOf(array_map(static fn (int $v): array => [$v, -$v], $this->elements));
	}

	/**
	 * @return list<int>
	 */
	public function benchFilter(): array
	{
		return $this->sequence->filter(static fn (int $v): bool => $v % 2 === 0)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchFilterNotNull(): array
	{
		return $this->sequence->filterNotNull()->toArray();
	}

	/**
	 * @return list<Item>
	 */
	public function benchFilterInstanceOf(): array
	{
		return $this->sequence->filterInstanceOf(Item::class)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchMap(): array
	{
		return $this->sequence->map(static fn (int $v): int => $v * 2)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchMapNotNull(): array
	{
		return $this->sequence->mapNotNull(static fn (int $v): ?int => $v % 2 === 0 ? $v : null)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchFlatMap(): array
	{
		return $this->sequence->flatMap(static fn (int $v): array => [$v, -$v])->toArray();
	}

	/**
	 * @return list<int>
	 */
	#[BeforeMethods('setUpPairs')]
	public function benchFlatten(): array
	{
		return $this->pairs->flatten()->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchTakeFirst(): array
	{
		return $this->sequence->takeFirst($this->half)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchDropFirst(): array
	{
		return $this->sequence->dropFirst($this->half)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchTakeWhile(): array
	{
		$probe = $this->probe;
		return $this->sequence->takeWhile(static fn (int $v): bool => $v !== $probe)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchDropWhile(): array
	{
		$probe = $this->probe;
		return $this->sequence->dropWhile(static fn (int $v): bool => $v !== $probe)->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchDistinct(): array
	{
		return $this->sequence->distinct()->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchDistinctBy(): array
	{
		return $this->sequence->distinctBy(static fn (int $v): int => $v % 1000)->toArray();
	}

	/**
	 * @return list<array{int, int}>
	 */
	public function benchZip(): array
	{
		return $this->sequence->zip($this->other)->toArray();
	}

	/**
	 * @return list<array{int, int}>
	 */
	public function benchZipWithNext(): array
	{
		return $this->sequence->zipWithNext()->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchOnEach(): array
	{
		$count = 0;
		return $this->sequence->onEach(static function (int $v) use (&$count): void {
			$count++;
		})->toArray();
	}
}
