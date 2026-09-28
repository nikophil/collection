<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Native;

use Noctud\Collection\Benchmarks\Fixture\Data;

/**
 * An array with string keys, as the reference for the Map benchmarks.
 */
final class NativeMapBench extends AbstractNativeBenchCase
{
	/** @var array<string, int> */
	private array $map;

	/** @var list<string> */
	private array $keys;

	/** @var list<string> */
	private array $newKeys;

	/** @var array<string, int> */
	private array $otherMap;

	/**
	 * @param array{size: positive-int} $params
	 */
	public function setUp(array $params): void
	{
		parent::setUp($params);

		$size = $params['size'];
		$this->keys = Data::strings($size);
		$this->map = array_combine($this->keys, $this->elements);
		$this->otherMap = array_combine(Data::strings($size, $this->half), Data::ints($size, $size));
		$this->newKeys = Data::strings(self::Batch, 2 * $size);
	}

	public function benchGet(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map[$this->keys[intdiv($i * $count, self::Batch)]];
		}

		return $sum;
	}

	public function benchContainsKey(): int
	{
		$found = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) array_key_exists($this->keys[intdiv($i * $count, self::Batch)], $this->map);
		}

		return $found;
	}

	public function benchContainsValue(): bool
	{
		return in_array($this->probe, $this->map, true);
	}

	/**
	 * @return array<string, int>
	 */
	public function benchFilter(): array
	{
		return array_filter($this->map, static fn (int $v, string $k): bool => $v % 2 === 0, ARRAY_FILTER_USE_BOTH);
	}

	/**
	 * @return array<string, int>
	 */
	public function benchMapValues(): array
	{
		return array_map(static fn (int $v): int => $v * 2, $this->map);
	}

	/**
	 * @return array<int, string>
	 */
	public function benchFlip(): array
	{
		return array_flip($this->map);
	}

	/**
	 * @return array<string, int>
	 */
	public function benchTakeFirst(): array
	{
		return array_slice($this->map, 0, $this->half, true);
	}

	/**
	 * @return array<string, int>
	 */
	public function benchSortedByKey(): array
	{
		$sorted = $this->map;
		ksort($sorted);
		return $sorted;
	}

	/**
	 * @return array<string, int>
	 */
	public function benchSortedByValue(): array
	{
		$sorted = $this->map;
		asort($sorted);
		return $sorted;
	}

	/**
	 * @return array<string, int>
	 */
	public function benchSortedWithValue(): array
	{
		$sorted = $this->map;
		uasort($sorted, static fn (int $a, int $b): int => $a <=> $b);
		return $sorted;
	}

	/**
	 * @return array<string, int>
	 */
	public function benchReversed(): array
	{
		return array_reverse($this->map, true);
	}

	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->map as $v) {
			$sum += $v;
		}

		return $sum;
	}

	/**
	 * @return list<string>
	 */
	public function benchKeysToArray(): array
	{
		return array_keys($this->map);
	}

	/**
	 * @return list<int>
	 */
	public function benchValuesToArray(): array
	{
		return array_values($this->map);
	}

	public function benchJsonEncode(): string
	{
		return json_encode($this->map, JSON_THROW_ON_ERROR);
	}

	/**
	 * @return array<string, int>
	 */
	public function benchPut(): array
	{
		$map = $this->map;
		foreach ($this->newKeys as $i => $key) {
			$map[$key] = $i;
		}

		return $map;
	}

	/**
	 * @return array<string, int>
	 */
	public function benchPutFirst(): array
	{
		$map = $this->map;
		foreach ($this->newKeys as $i => $key) {
			$map = [$key => $i] + $map;
		}

		return $map;
	}

	/**
	 * @return array<string, int>
	 */
	public function benchPutAll(): array
	{
		return array_merge($this->map, $this->otherMap);
	}

	/**
	 * @return array<string, int>
	 */
	public function benchRemove(): array
	{
		$map = $this->map;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			unset($map[$this->keys[intdiv($i * $count, self::Batch)]]);
		}

		return $map;
	}

	/**
	 * @return array<string, int>
	 */
	public function benchSortByKey(): array
	{
		$map = $this->map;
		ksort($map);
		return $map;
	}
}
