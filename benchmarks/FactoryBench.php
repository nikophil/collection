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
use Noctud\Collection\List\MutableList;
use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\Map\MutableMap;
use Noctud\Collection\Set\ImmutableSet;
use Noctud\Collection\Set\MutableSet;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Revs;
use function Noctud\Collection\generateSequence;
use function Noctud\Collection\intMapOf;
use function Noctud\Collection\listOf;
use function Noctud\Collection\mapOf;
use function Noctud\Collection\mapOfPairs;
use function Noctud\Collection\mutableIntMapOf;
use function Noctud\Collection\mutableListOf;
use function Noctud\Collection\mutableMapOf;
use function Noctud\Collection\mutableMapOfPairs;
use function Noctud\Collection\mutableSetOf;
use function Noctud\Collection\mutableStringMapOf;
use function Noctud\Collection\sequenceOf;
use function Noctud\Collection\setOf;
use function Noctud\Collection\stringMapOf;

/**
 * Construction through the factory functions, from an array unless stated otherwise.
 * Lazy variants include the first access, which materializes the collection.
 */
#[Groups(['factory', 'guard'])]
#[BeforeMethods('setUp')]
#[ParamProviders('provideSizes')]
final class FactoryBench
{
	use ProvidesSizes;

	/** Revolutions for subjects of a few microseconds at most, too fast to be measured reliably with the default. */
	private const int FastSubjectRevs = 1000;

	/** @var list<int> */
	private array $elements;

	/** @var array<string, int> */
	private array $stringEntries;

	/** @var array<int, int> */
	private array $intEntries;

	/** @var list<array{string, int}> */
	private array $pairs;

	/**
	 * @param array{size: positive-int} $params
	 */
	public function setUp(array $params): void
	{
		$size = $params['size'];
		$this->elements = Data::ints($size);
		$this->stringEntries = array_combine(Data::strings($size), $this->elements);
		$this->intEntries = array_combine($this->elements, $this->elements);
		$this->pairs = [];
		foreach ($this->stringEntries as $key => $value) {
			$this->pairs[] = [$key, $value];
		}
	}

	/**
	 * @return ImmutableList<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchListOf(): ImmutableList
	{
		return listOf($this->elements);
	}

	/**
	 * @return ImmutableList<int>
	 */
	public function benchListOfGenerator(): ImmutableList
	{
		return listOf($this->generate());
	}

	#[Revs(self::FastSubjectRevs)]
	public function benchListOfLazy(): int
	{
		$elements = $this->elements;
		return listOf(static fn (): array => $elements)->count();
	}

	/**
	 * @return MutableList<int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchMutableListOf(): MutableList
	{
		return mutableListOf($this->elements);
	}

	/**
	 * @return ImmutableSet<int>
	 */
	public function benchSetOf(): ImmutableSet
	{
		return setOf($this->elements);
	}

	/**
	 * @return ImmutableSet<int>
	 */
	public function benchSetOfGenerator(): ImmutableSet
	{
		return setOf($this->generate());
	}

	public function benchSetOfLazy(): int
	{
		$elements = $this->elements;
		return setOf(static fn (): array => $elements)->count();
	}

	/**
	 * @return MutableSet<int>
	 */
	public function benchMutableSetOf(): MutableSet
	{
		return mutableSetOf($this->elements);
	}

	/**
	 * @return ImmutableMap<string, int>
	 */
	public function benchMapOf(): ImmutableMap
	{
		return mapOf($this->stringEntries);
	}

	public function benchMapOfLazy(): int
	{
		$entries = $this->stringEntries;
		return mapOf(static fn (): array => $entries)->count();
	}

	/**
	 * @return MutableMap<string, int>
	 */
	public function benchMutableMapOf(): MutableMap
	{
		return mutableMapOf($this->stringEntries);
	}

	/**
	 * @return ImmutableMap<string, int>
	 */
	public function benchMapOfPairs(): ImmutableMap
	{
		return mapOfPairs($this->pairs);
	}

	/**
	 * @return MutableMap<string, int>
	 */
	public function benchMutableMapOfPairs(): MutableMap
	{
		return mutableMapOfPairs($this->pairs);
	}

	/**
	 * @return ImmutableMap<string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchStringMapOf(): ImmutableMap
	{
		return stringMapOf($this->stringEntries);
	}

	/**
	 * @return MutableMap<string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchMutableStringMapOf(): MutableMap
	{
		return mutableStringMapOf($this->stringEntries);
	}

	/**
	 * @return ImmutableMap<int, int>
	 */
	public function benchIntMapOf(): ImmutableMap
	{
		return intMapOf($this->intEntries);
	}

	/**
	 * @return MutableMap<int, int>
	 */
	public function benchMutableIntMapOf(): MutableMap
	{
		return mutableIntMapOf($this->intEntries);
	}

	/**
	 * @return list<int>
	 */
	public function benchSequenceOfGenerator(): array
	{
		return sequenceOf($this->generate())->toArray();
	}

	/**
	 * @return list<int>
	 */
	public function benchGenerateSequence(): array
	{
		return generateSequence(0, static fn (int $v): int => $v + 1)->takeFirst(count($this->elements))->toArray();
	}

	/**
	 * @return Generator<int, int>
	 */
	private function generate(): Generator
	{
		yield from $this->elements;
	}
}
