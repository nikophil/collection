<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection;

use Closure;
use Noctud\Collection\Exception\NoSuchElementException;
use Noctud\Collection\Sequence\Sequence;

/**
 * Terminal operations implemented by walking $this and nothing else, shared by the eager
 * Collection side and the lazy Sequence side.
 *
 * A body belongs here only if it is identical for both, which excludes the family the eager side
 * answers from its store in O(1) (first/last/contains/count). A message naming the subject is no
 * longer a reason to split: the exception derives that noun from $this.
 *
 * Consumers declare the contract, so the PHPDoc here is {@inheritDoc}: it resolves against
 * Collection<E> or Sequence<E> depending on who uses the trait.
 *
 * @template E
 *
 * @internal
 */
trait IterableTerminalsLogic
{
	/** {@inheritDoc} */
	public function single()
	{
		$found = false;
		$result = null;

		foreach ($this as $v) {
			if ($found) {
				throw NoSuchElementException::subjectHasMoreThanOneElement($this);
			}

			$result = $v;
			$found = true;
		}

		if (!$found) {
			throw NoSuchElementException::emptySubject($this);
		}

		return $result; // @phpstan-ignore return.type
	}

	/**
	 * {@inheritDoc}
	 *
	 * Not a try-catch around single(): iterating $this runs user closures on the lazy side, and a
	 * NoSuchElementException raised inside one is a real error, not an answer to this question.
	 */
	public function singleOrNull(): mixed
	{
		$found = false;
		$result = null;

		foreach ($this as $v) {
			if ($found) {
				return null;
			}

			$result = $v;
			$found = true;
		}

		return $result;
	}

	/** {@inheritDoc} */
	public function find(Closure $predicate): mixed
	{
		foreach ($this as $i => $v) {
			if ($predicate($v, $i)) {
				return $v;
			}
		}

		return null;
	}

	/** {@inheritDoc} */
	public function expect(Closure $predicate)
	{
		foreach ($this as $i => $v) {
			if ($predicate($v, $i)) {
				return $v;
			}
		}

		throw new NoSuchElementException('No element matching the predicate was found');
	}
}
