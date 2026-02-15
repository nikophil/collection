<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Operation;

use Noctud\Collection\Exception\ConversionException;
use Noctud\Collection\KeyHasher;
use Noctud\Collection\Map\HashMap\HashKeyValueStore;
use Noctud\Collection\Map\KeyCollisionStrategy;

/**
 * @internal
 * @template K of string|int|bool|float|object
 * @template V
 * @extends AbstractKeyValueOperation<K,V>
 */
final class FlipKeyValueOperation extends AbstractKeyValueOperation
{
	/**
	 * @return HashKeyValueStore<V, K>
	 */
	public function items(KeyCollisionStrategy $strategy): HashKeyValueStore // @phpstan-ignore generics.notSubtype
	{
		/** @var HashKeyValueStore<V, K> $store */
		$store = HashKeyValueStore::empty(); // @phpstan-ignore generics.notSubtype

		foreach ($this->data as $k => $v) {
			if (!is_string($v) && !is_int($v) && !is_bool($v) && !is_float($v) && !is_object($v)) {
				KeyHasher::hashMapKey($v); // throws InvalidKeyTypeException with proper message
			}

			if ($strategy !== KeyCollisionStrategy::KeepLast && $store->containsKey($v)) {
				if ($strategy === KeyCollisionStrategy::Throw) {
					throw new ConversionException(sprintf(
						'Key collision detected during flip. Value "%s" appears multiple times and would cause a key collision.',
						is_scalar($v) || $v === null ? (string) $v : get_debug_type($v)
					));
				}

				// KeepFirst: skip duplicate
				continue;
			}

			$store->put($v, $k);
		}

		return $store;
	}
}
