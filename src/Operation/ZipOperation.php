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

		// Positioning the other side would pull an element no pair could ever use.
		if (!$left->valid()) {
			return;
		}

		$right = self::cursor($other);

		// This side is known to hold an element on every entry: the check above covers the
		// first one, the break below every later one.
		while ($right->valid()) {
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
	 * A Generator is always positioned - valid() primes it - so valid() === false means
	 * exhausted, never "not started". Left alone, it resumes from wherever it stands, which is
	 * what lets a caller consume the head of a stream and zip the rest.
	 *
	 * Every other Iterator holds no position until rewound: an SplDoublyLinkedList and every
	 * IteratorIterator decorator answer valid() === false beforehand, which lockstep would read
	 * as an empty side. They are rewound, so one already advanced restarts from its first
	 * element - it cannot be told apart from a fresh one - and NoRewindIterator opts out.
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

		if ($iterable instanceof Generator) {
			return $iterable;
		}

		$cursor = $iterable instanceof Iterator ? $iterable : new IteratorIterator($iterable);
		$cursor->rewind();

		return $cursor;
	}
}
