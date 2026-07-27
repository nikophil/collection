<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Operation;

use ArrayIterator;
use Generator;
use Iterator;
use IteratorIterator;

/**
 * @internal
 * @template V
 * @extends AbstractOperation<int,V>
 */
final class ZipOperation extends AbstractOperation
{
	/**
	 * @template U
	 * @param iterable<U> $other
	 * @return Generator<array{V, U}>
	 */
	public function with(iterable $other): Generator
	{
		$left = self::cursor($this->data);
		$right = self::cursor($other);

		while ($left->valid() && $right->valid()) {
			yield [$left->current(), $right->current()];

			// Advancing the other side only once this one still has an element spares it a
			// pull that would be thrown away whenever this side is the shorter one.
			$left->next();

			if (!$left->valid()) {
				break;
			}

			$right->next();
		}
	}

	/**
	 * Positioned cursor over any iterable, so both sides can be walked in lockstep without
	 * either being buffered.
	 *
	 * An Iterator is used as is and never rewound: it may be a cursor that has already
	 * started, and rewinding a running Generator throws.
	 *
	 * @template T
	 * @param iterable<T> $iterable
	 * @return Iterator<T>
	 */
	private static function cursor(iterable $iterable): Iterator
	{
		if (is_array($iterable)) {
			return new ArrayIterator($iterable);
		}

		if ($iterable instanceof Iterator) {
			return $iterable;
		}

		// An IteratorAggregate only hands out its iterator on rewind(): before that the
		// wrapper has no position at all, and valid() answers false.
		$cursor = new IteratorIterator($iterable);
		$cursor->rewind();

		return $cursor;
	}
}
