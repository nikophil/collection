<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Operation;

use Generator;
use Noctud\Collection\KeyHasher;

/**
 * @internal
 * @template V
 * @extends AbstractOperation<int,V>
 */
final class DistinctOperation extends AbstractOperation
{
	/**
	 * @return Generator<V>
	 */
	public function items(): Generator
	{
		$set = [];
		foreach ($this->data as $v) {
			$nk = KeyHasher::hashSetKey($v);
			if (!isset($set[$nk])) {
				$set[$nk] = true;
				yield $v;
			}
		}
	}

	/**
	 * @template SK
	 * @param callable(V, int):SK $selector
	 * @return Generator<V>
	 */
	public function bySelector(callable $selector): Generator
	{
		$set = [];
		foreach ($this->data as $i => $v) {
			$nk = KeyHasher::hashSetKey($selector($v, $i));
			if (!isset($set[$nk])) {
				$set[$nk] = true;
				yield $v;
			}
		}
	}
}
