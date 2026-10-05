<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Set;

use Noctud\Collection\Benchmarks\Fixture\ProvidesElementTypes;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Set\ImmutableSet;
use Noctud\Collection\Set\MutableSet;
use Noctud\Collection\Set\Set;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use function Noctud\Collection\listOf;
use function Noctud\Collection\mutableSetOf;
use function Noctud\Collection\setOf;

/**
 * Operations whose cost depends on hashing the elements, for each element type.
 * Object elements overlapping with the other half are the same instances, so identity matches.
 *
 * @phpstan-import-type ElementType from ProvidesElementTypes
 */
#[Groups(['set', 'hashing', 'guard'])]
#[BeforeMethods('setUp')]
#[ParamProviders(['provideSizes', 'provideElementTypes'])]
final class SetElementTypeBench
{
	use ProvidesElementTypes;
	use ProvidesSizes;

	private const int Batch = 100;

	/** @var list<mixed> */
	private array $elements;

	/** @var list<mixed> */
	private array $other;

	/** @var ImmutableSet<mixed> */
	private ImmutableSet $set;

	/** @var ImmutableList<mixed> */
	private ImmutableList $list;

	/**
	 * @param array{size: positive-int, type: ElementType} $params
	 */
	public function setUp(array $params): void
	{
		$size = $params['size'];
		$all = self::elementsOfType($params['type'], $size + ($size >> 1));
		$this->elements = array_slice($all, 0, $size);
		$this->other = array_slice($all, $size >> 1);
		$this->set = setOf($this->elements);
		$this->list = listOf([...$this->elements, ...$this->elements]);
	}

	/**
	 * @return ImmutableSet<mixed>
	 */
	public function benchSetOf(): ImmutableSet
	{
		return setOf($this->elements);
	}

	public function benchContains(): int
	{
		$found = 0;
		$count = count($this->elements);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) $this->set->contains($this->elements[intdiv($i * $count, self::Batch)]);
		}

		return $found;
	}

	/**
	 * @return MutableSet<mixed>
	 */
	public function benchAdd(): MutableSet
	{
		$set = mutableSetOf($this->set);
		for ($i = 0; $i < self::Batch; $i++) {
			$set->add($this->other[count($this->other) - 1 - $i]);
		}

		return $set;
	}

	/**
	 * @return Set<mixed>
	 */
	public function benchIntersect(): Set
	{
		return $this->set->intersect($this->other);
	}

	/**
	 * A list holding every element twice.
	 *
	 * @return ImmutableList<mixed>
	 */
	public function benchListDistinct(): ImmutableList
	{
		return $this->list->distinct();
	}
}
