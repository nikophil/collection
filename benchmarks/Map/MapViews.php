<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

/**
 * Reads through the $keys, $values and $entries views. The views expose the whole
 * Collection API, already covered by the List and Set benchmarks: only the access
 * patterns specific to a map are measured here.
 */
trait MapViews
{
	public function benchKeysIterate(): int
	{
		$count = 0;
		foreach ($this->map->keys as $k) {
			$count++;
		}

		return $count;
	}

	public function benchKeysContains(): int
	{
		$found = 0;
		$count = count($this->keys);
		for ($i = 0; $i < self::Batch; $i++) {
			$found += (int) $this->map->keys->contains($this->keys[intdiv($i * $count, self::Batch)]);
		}

		return $found;
	}

	/**
	 * @return list<int|string>
	 */
	public function benchKeysToArray(): array
	{
		return $this->map->keys->toArray();
	}

	public function benchValuesIterate(): int
	{
		$sum = 0;
		foreach ($this->map->values as $v) {
			$sum += $v;
		}

		return $sum;
	}

	public function benchValuesSum(): int|float
	{
		return $this->map->values->sum();
	}

	/**
	 * @return list<int>
	 */
	public function benchValuesToArray(): array
	{
		return $this->map->values->toArray();
	}

	public function benchEntriesIterate(): int
	{
		$sum = 0;
		foreach ($this->map->entries as $entry) {
			$sum += $entry->value;
		}

		return $sum;
	}
}
