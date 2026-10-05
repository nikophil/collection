<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\Map\MutableMap;
use PhpBench\Attributes\Revs;

/**
 * Iteration and conversion to other structures.
 */
trait MapConvert
{
	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->map as $v) {
			$sum += $v;
		}

		return $sum;
	}

	public function benchForEach(): int
	{
		$sum = 0;
		$this->map->forEach(static function (int $v, int|string $k) use (&$sum): void {
			$sum += $v;
		});

		return $sum;
	}

	public function benchForEachKey(): int
	{
		$count = 0;
		$this->map->forEachKey(static function (int|string $k) use (&$count): void {
			$count++;
		});

		return $count;
	}

	public function benchForEachValue(): int
	{
		$sum = 0;
		$this->map->forEachValue(static function (int $v) use (&$sum): void {
			$sum += $v;
		});

		return $sum;
	}

	/**
	 * @return array<int|string, int>
	 */
	public function benchToArray(): array
	{
		return $this->map->toArray();
	}

	/**
	 * @return list<array{int|string, int}>
	 */
	public function benchToPairs(): array
	{
		return $this->map->toPairs();
	}

	/**
	 * @return MutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchToMutable(): MutableMap
	{
		return $this->map->toMutable();
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchToImmutable(): ImmutableMap
	{
		return $this->map->toImmutable();
	}

	public function benchJsonEncode(): string
	{
		return json_encode($this->map, JSON_THROW_ON_ERROR);
	}
}
