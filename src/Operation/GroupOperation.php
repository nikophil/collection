<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Operation;

use Noctud\Collection\Map\HashMap\HashKeyValueStore;

/**
 * @internal
 * @template V
 * @extends AbstractOperation<int,V>
 */
final class GroupOperation extends AbstractOperation
{
	/**
	 * Accumulates elements into one bucket per key, and hands the buckets back as plain
	 * arrays: wrapping them into collections is the caller's job, the way
	 * PartitionOperation returns plain lists.
	 *
	 * @template K of string|int|bool|float|object
	 * @template T
	 * @param callable(V, int):K $keySelector
	 * @param (callable(V, int):T)|null $valueTransform
	 * @return HashKeyValueStore<K, list<V|T>>
	 */
	public function byKey(callable $keySelector, ?callable $valueTransform = null): HashKeyValueStore
	{
		/** @var HashKeyValueStore<K, list<V|T>> $store */
		$store = HashKeyValueStore::empty();

		foreach ($this->data as $i => $v) {
			$key = $keySelector($v, $i);
			/** @var list<V|T> $bucket */
			$bucket = $store->get($key) ?? [];
			$bucket[] = $valueTransform !== null ? $valueTransform($v, $i) : $v;
			$store->put($key, $bucket);
		}

		return $store;
	}

	/**
	 * Counts elements per key without ever holding a bucket - the group sizes are all
	 * groupBy()->map(count) ever needed.
	 *
	 * @template K of string|int|bool|float|object
	 * @param callable(V, int):K $keySelector
	 * @return HashKeyValueStore<K, int>
	 */
	public function countByKey(callable $keySelector): HashKeyValueStore
	{
		/** @var HashKeyValueStore<K, int> $store */
		$store = HashKeyValueStore::empty();

		foreach ($this->data as $i => $v) {
			$key = $keySelector($v, $i);
			/** @var int $count */
			$count = $store->get($key) ?? 0;
			$store->put($key, $count + 1);
		}

		return $store;
	}
}
