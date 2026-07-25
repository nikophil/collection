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
final class SetOperation extends AbstractOperation
{
	/**
	 * @template U
	 * @param iterable<U> $other
	 * @return Generator<V&U>
	 */
	public function intersect(iterable $other): Generator
	{
		$otherSet = [];
		foreach ($other as $v) {
			$otherSet[KeyHasher::hashSetKey($v)] = true;
		}

		$seen = [];
		foreach ($this->data as $v) {
			$hash = KeyHasher::hashSetKey($v);
			if (isset($otherSet[$hash]) && !isset($seen[$hash])) {
				$seen[$hash] = true;
				// Hash membership in $otherSet means the value also occurs in $other.
				yield $v; // @phpstan-ignore generator.valueType
			}
		}
	}

	/**
	 * @template U
	 * @param iterable<U> $other
	 * @return Generator<V|U>
	 */
	public function union(iterable $other): Generator
	{
		$seen = [];
		foreach ($this->data as $v) {
			$hash = KeyHasher::hashSetKey($v);
			if (!isset($seen[$hash])) {
				$seen[$hash] = true;
				yield $v;
			}
		}

		foreach ($other as $v) {
			$hash = KeyHasher::hashSetKey($v);
			if (!isset($seen[$hash])) {
				$seen[$hash] = true;
				yield $v;
			}
		}
	}

	/**
	 * @param iterable<mixed> $other
	 * @return Generator<V>
	 */
	public function subtract(iterable $other): Generator
	{
		$otherSet = [];
		foreach ($other as $v) {
			$otherSet[KeyHasher::hashSetKey($v)] = true;
		}

		$seen = [];
		foreach ($this->data as $v) {
			$hash = KeyHasher::hashSetKey($v);
			if (!isset($otherSet[$hash]) && !isset($seen[$hash])) {
				$seen[$hash] = true;
				yield $v;
			}
		}
	}
}
