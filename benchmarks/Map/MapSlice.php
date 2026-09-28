<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\Map;

/**
 * Taking or dropping half of the map, by count or by predicate.
 */
trait MapSlice
{
	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchTakeFirst(): Map
	{
		return $this->map->takeFirst($this->half);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchDropFirst(): Map
	{
		return $this->map->dropFirst($this->half);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchTakeLast(): Map
	{
		return $this->map->takeLast($this->half);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchDropLast(): Map
	{
		return $this->map->dropLast($this->half);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchTakeWhile(): Map
	{
		$probeKey = $this->probeKey;
		return $this->map->takeWhile(static fn (int $v, int|string $k): bool => $k !== $probeKey);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchDropWhile(): Map
	{
		$probeKey = $this->probeKey;
		return $this->map->dropWhile(static fn (int $v, int|string $k): bool => $k !== $probeKey);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchTakeLastWhile(): Map
	{
		$probeKey = $this->probeKey;
		return $this->map->takeLastWhile(static fn (int $v, int|string $k): bool => $k !== $probeKey);
	}

	/**
	 * @return Map<covariant int|string, int>
	 */
	public function benchDropLastWhile(): Map
	{
		$probeKey = $this->probeKey;
		return $this->map->dropLastWhile(static fn (int $v, int|string $k): bool => $k !== $probeKey);
	}
}
