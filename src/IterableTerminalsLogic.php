<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection;

use Closure;
use Noctud\Collection\Exception\NoSuchElementException;

/**
 * Terminal operations implemented by walking $this and nothing else, shared by the eager
 * Collection side and the lazy Sequence side.
 *
 * A body belongs here only if it is identical for both, which excludes two families: the one the
 * eager side answers from its store in O(1) (first/last/isEmpty/contains/count), and containsAll,
 * whose one-lookup-per-value shape would cost a sequence one pass per value. A message naming the
 * subject is no longer a reason to split: the exception derives that noun from $this.
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
	// --- Element Access ---

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

	// --- Querying ---

	/** {@inheritDoc} */
	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	/** {@inheritDoc} */
	public function all(Closure $predicate): bool
	{
		foreach ($this as $i => $v) {
			if (!$predicate($v, $i)) {
				return false;
			}
		}

		return true;
	}

	/** {@inheritDoc} */
	public function any(Closure $predicate): bool
	{
		foreach ($this as $i => $v) {
			if ($predicate($v, $i)) {
				return true;
			}
		}

		return false;
	}

	/** {@inheritDoc} */
	public function none(Closure $predicate): bool
	{
		return !$this->any($predicate);
	}

	/** {@inheritDoc} */
	public function countWhere(Closure $predicate): int
	{
		/** @var int<0, max> $count */
		$count = 0;
		foreach ($this as $i => $v) {
			if ($predicate($v, $i)) {
				$count++;
			}
		}

		return $count;
	}
}
