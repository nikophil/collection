<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Fixture;

use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Deterministic data sets, so that two runs (e.g. a baseline and a comparison) work on the same input.
 */
final class Data
{
	private const int Seed = 42;

	/**
	 * Unique integers from $offset to $offset + $size - 1, in a deterministic shuffled order.
	 *
	 * @return ($size is positive-int ? non-empty-list<int> : list<int>)
	 */
	public static function ints(int $size, int $offset = 0): array
	{
		if ($size === 0) {
			return [];
		}

		return self::shuffle(range($offset, $offset + $size - 1));
	}

	/**
	 * @return list<string>
	 */
	public static function strings(int $size, int $offset = 0): array
	{
		return array_map(static fn (int $i): string => "item-$i", self::ints($size, $offset));
	}

	/**
	 * @return list<float>
	 */
	public static function floats(int $size, int $offset = 0): array
	{
		return array_map(static fn (int $i): float => $i + 0.5, self::ints($size, $offset));
	}

	/**
	 * @return list<Item>
	 */
	public static function objects(int $size, int $offset = 0): array
	{
		return array_map(static fn (int $i): Item => new Item($i), self::ints($size, $offset));
	}

	/**
	 * @return list<HashableItem>
	 */
	public static function hashables(int $size, int $offset = 0): array
	{
		return array_map(static fn (int $i): HashableItem => new HashableItem($i), self::ints($size, $offset));
	}

	/**
	 * @return list<array{int, int}>
	 */
	public static function arrays(int $size, int $offset = 0): array
	{
		return array_map(static fn (int $i): array => [$i, $i * 2], self::ints($size, $offset));
	}

	/**
	 * @template T
	 * @param list<T> $values
	 * @return list<T>
	 */
	private static function shuffle(array $values): array
	{
		return new Randomizer(new Mt19937(self::Seed))->shuffleArray($values);
	}
}
