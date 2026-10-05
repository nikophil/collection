<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks;

use Generator;
use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use Noctud\Collection\List\ImmutableList;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Revs;
use function Noctud\Collection\listOf;

/**
 * The same map -> filter chain run eagerly on a List, lazily on a Sequence, with native array
 * functions and with native generators. The "Full" subjects drain the whole chain, the
 * "TakeFirst" ones only need the first matching elements, where laziness pays off.
 */
#[Groups(['pipeline'])]
#[BeforeMethods('setUp')]
#[ParamProviders('provideSizes')]
final class PipelineBench
{
	use ProvidesSizes;

	private const int Take = 10;

	/** Revolutions for subjects of a few microseconds at most, too fast to be measured reliably with the default. */
	private const int FastSubjectRevs = 1000;

	/** @var list<int> */
	private array $elements;

	/** @var ImmutableList<int> */
	private ImmutableList $list;

	/**
	 * @param array{size: positive-int} $params
	 */
	public function setUp(array $params): void
	{
		$this->elements = Data::ints($params['size']);
		$this->list = listOf($this->elements);
	}

	/**
	 * @return list<int>
	 */
	#[Groups(['guard'])]
	public function benchListFull(): array
	{
		return $this->list
			->map(static fn (int $v): int => $v * 3)
			->filter(static fn (int $v): bool => $v % 2 === 0)
			->toArray();
	}

	/**
	 * @return list<int>
	 */
	#[Groups(['guard'])]
	public function benchSequenceFull(): array
	{
		return $this->list->asSequence()
			->map(static fn (int $v): int => $v * 3)
			->filter(static fn (int $v): bool => $v % 2 === 0)
			->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchNativeArrayFull(): array
	{
		return array_values(array_filter(
			array_map(static fn (int $v): int => $v * 3, $this->elements),
			static fn (int $v): bool => $v % 2 === 0,
		));
	}

	/**
	 * @return list<int>
	 */
	public function benchNativeGeneratorFull(): array
	{
		return iterator_to_array($this->generate(), false);
	}

	/**
	 * @return list<int>
	 */
	#[Groups(['guard'])]
	public function benchListTakeFirst(): array
	{
		return $this->list
			->map(static fn (int $v): int => $v * 3)
			->filter(static fn (int $v): bool => $v % 2 === 0)
			->takeFirst(self::Take)
			->toArray();
	}

	/**
	 * @return list<int>
	 */
	#[Groups(['guard'])]
	public function benchSequenceTakeFirst(): array
	{
		return $this->list->asSequence()
			->map(static fn (int $v): int => $v * 3)
			->filter(static fn (int $v): bool => $v % 2 === 0)
			->takeFirst(self::Take)
			->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchNativeArrayTakeFirst(): array
	{
		return array_slice(array_values(array_filter(
			array_map(static fn (int $v): int => $v * 3, $this->elements),
			static fn (int $v): bool => $v % 2 === 0,
		)), 0, self::Take);
	}

	/**
	 * @return list<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchNativeGeneratorTakeFirst(): array
	{
		$result = [];
		foreach ($this->generate() as $v) {
			$result[] = $v;
			if (count($result) === self::Take) {
				break;
			}
		}

		return $result;
	}

	/**
	 * @return Generator<int>
	 */
	private function generate(): Generator
	{
		foreach ($this->elements as $v) {
			$v *= 3;
			if ($v % 2 === 0) {
				yield $v;
			}
		}
	}
}
