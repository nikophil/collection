<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Map\ImmutableMap;
use PhpBench\Attributes\Revs;

/**
 * Writes on an immutable map, each returning a modified copy.
 */
trait MapImmutableWrite
{
	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchPut(): ImmutableMap
	{
		return $this->map->put($this->newKeys[0], -1);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchPutFirst(): ImmutableMap
	{
		return $this->map->putFirst($this->newKeys[0], -1);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchPutIfAbsent(): ImmutableMap
	{
		return $this->map->putIfAbsent($this->newKeys[0], -1);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	public function benchPutAll(): ImmutableMap
	{
		return $this->map->putAll($this->other);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	public function benchPutAllPairs(): ImmutableMap
	{
		return $this->map->putAllPairs($this->otherPairs);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemove(): ImmutableMap
	{
		return $this->map->remove($this->probeKey);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveFirst(): ImmutableMap
	{
		return $this->map->removeFirst();
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	#[Revs(self::FastSubjectRevs)]
	public function benchRemoveLast(): ImmutableMap
	{
		return $this->map->removeLast();
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	public function benchRemoveIf(): ImmutableMap
	{
		return $this->map->removeIf(static fn (int $v, int|string $k): bool => $v % 2 === 0);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	public function benchRemoveIfKey(): ImmutableMap
	{
		$probeKey = $this->probeKey;
		return $this->map->removeIfKey(static fn (int|string $k): bool => $k < $probeKey);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	public function benchRemoveIfValue(): ImmutableMap
	{
		return $this->map->removeIfValue(static fn (int $v): bool => $v % 2 === 0);
	}

	/**
	 * @return ImmutableMap<covariant int|string, int>
	 */
	public function benchRemoveNullValues(): ImmutableMap
	{
		return $this->map->removeNullValues();
	}
}
