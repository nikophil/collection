<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Benchmarks\Fixture\Data;
use Noctud\Collection\Benchmarks\Fixture\ProvidesElementTypes;
use Noctud\Collection\Benchmarks\Fixture\ProvidesSizes;
use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\Map\MutableMap;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use function Noctud\Collection\mapOfPairs;
use function Noctud\Collection\mutableMapOf;

/**
 * HashMap operations whose cost depends on hashing the keys, for each key type.
 * The maps are built from pairs, since objects and floats cannot be PHP array keys.
 *
 * @phpstan-import-type KeyType from ProvidesElementTypes
 */
#[Groups(['map', 'hashing', 'guard'])]
#[BeforeMethods('setUp')]
#[ParamProviders(['provideSizes', 'provideKeyTypes'])]
final class MapKeyTypeBench
{
	use ProvidesElementTypes;
	use ProvidesSizes;

	private const int Batch = 100;

	/** @var list<int|string|float|object> */
	private array $keys;

	/** @var list<int|string|float|object> Keys absent from the map, one per call of a batch. */
	private array $newKeys;

	/** @var list<array{int|string|float|object, int}> */
	private array $pairs;

	/** @var ImmutableMap<int|string|float|object, int> */
	private ImmutableMap $map;

	/**
	 * @param array{size: positive-int, type: KeyType} $params
	 */
	public function setUp(array $params): void
	{
		$size = $params['size'];
		$this->keys = self::keysOfType($params['type'], $size);
		$this->newKeys = self::keysOfType($params['type'], self::Batch, $size);
		$values = Data::ints($size);
		$this->pairs = [];
		foreach ($this->keys as $i => $key) {
			$this->pairs[] = [$key, $values[$i]];
		}
		$this->map = mapOfPairs($this->pairs);
	}

	/**
	 * @return ImmutableMap<int|string|float|object, int>
	 */
	public function benchMapOfPairs(): ImmutableMap
	{
		return mapOfPairs($this->pairs);
	}

	public function benchGet(): int
	{
		$sum = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$sum += $this->map->get($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $sum;
	}

	public function benchContainsKey(): int
	{
		$found = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) $this->map->containsKey($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $found;
	}

	/**
	 * @return MutableMap<int|string|float|object, int>
	 */
	public function benchPut(): MutableMap
	{
		$map = mutableMapOf($this->map);
		foreach ($this->newKeys as $i => $key) {
			$map->put($key, $i);
		}

		return $map;
	}

	/**
	 * @return MutableMap<int|string|float|object, int>
	 */
	public function benchRemove(): MutableMap
	{
		$map = mutableMapOf($this->map);
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$map->remove($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $map;
	}

	public function benchIterate(): int
	{
		$sum = 0;
		foreach ($this->map as $v) {
			$sum += $v;
		}

		return $sum;
	}
}
