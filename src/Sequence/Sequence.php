<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Sequence;

use Closure;
use IteratorAggregate;
use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Set\ImmutableSet;
use NoDiscard;

/**
 * Lazily evaluated stream of values.
 *
 * Nothing is pulled from the source before the sequence is iterated (a foreach or a
 * terminal operation). A sequence guarantees at least one pass; whether it can be
 * iterated again depends on its source:
 *
 * - an array: the sequence replays, pulling fresh elements every pass;
 * - a Closure or an IteratorAggregate (e.g. a Collection): a producer, asked for an
 *   iterable on every pass. Whatever it does to build that iterable runs again each
 *   time - if it fires a SQL query, that query is re-executed on every iteration of
 *   the sequence. It must hand back a fresh iterator on each call; handing back the
 *   iterator of the previous pass (a getIterator() returning a Generator it keeps
 *   around, for instance) throws NonReplayableSourceException;
 * - a raw Iterator/Generator: the sequence is single-pass and any further iteration
 *   throws NonReplayableSourceException (a partial pass counts as consumed).
 *
 * Keys are positional: every pass yields fresh 0..n keys, whatever the source yields.
 *
 * Deliberately neither Countable (counting would silently consume a pass; native
 * count($seq) is a TypeError by design) nor JsonSerializable
 * (json_encode would be a hidden materialization) - materialize explicitly with
 * toList() or toArray() instead.
 *
 * @template E
 * @extends IteratorAggregate<int, E>
 */
interface Sequence extends IteratorAggregate
{
	// --- Transformation ---

	/**
	 * Filter elements by predicate.
	 *
	 * @param Closure(E, int):bool $predicate
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function filter(Closure $predicate): Sequence;

	/**
	 * Filter non-null elements.
	 *
	 * @return Sequence<(E is null ? never : E)>
	 */
	#[NoDiscard]
	public function filterNotNull(): Sequence;

	/**
	 * Filter elements that are instances of the given class or interface.
	 *
	 * @template T
	 * @param class-string<T> $type
	 * @return Sequence<T>
	 */
	#[NoDiscard]
	public function filterInstanceOf(string $type): Sequence;

	/**
	 * Map elements to a new sequence.
	 * The transform receives the element and optionally the index.
	 *
	 * @template R
	 * @param Closure(E, int):R $transform
	 * @return Sequence<R>
	 */
	#[NoDiscard]
	public function map(Closure $transform): Sequence;

	/**
	 * Transforms each element using the given function and excludes null results.
	 * Combines map and filterNotNull in a single operation.
	 *
	 * @template R
	 * @param Closure(E, int):(R|null) $transform
	 * @return Sequence<R>
	 */
	#[NoDiscard]
	public function mapNotNull(Closure $transform): Sequence;

	/**
	 * Flat map elements to a new sequence.
	 * Each iterable returned by the transform is consumed lazily, element by element.
	 *
	 * @template R
	 * @param Closure(E, int):iterable<R> $transform
	 * @return Sequence<R>
	 */
	#[NoDiscard]
	public function flatMap(Closure $transform): Sequence;

	/**
	 * Flatten a sequence of iterables into a single sequence.
	 * Iterable elements are flattened one level; non-iterable elements are kept as-is.
	 * The array{} in value-of keeps the type resolvable when E is never (empty sequences).
	 *
	 * @return Sequence<(E is iterable<mixed> ? value-of<E|array{}> : E)>
	 */
	#[NoDiscard]
	public function flatten(): Sequence;

	/**
	 * Take the first N elements.
	 * The source is not pulled any further once N elements have been yielded.
	 *
	 * @param non-negative-int $n
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function takeFirst(int $n = 1): Sequence;

	/**
	 * Drops the first N elements.
	 *
	 * @param non-negative-int $n
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function dropFirst(int $n = 1): Sequence;

	/**
	 * Takes elements while the predicate is true.
	 * The source is not pulled any further once the predicate has returned false.
	 *
	 * @param Closure(E, int):bool $predicate
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function takeWhile(Closure $predicate): Sequence;

	/**
	 * Drops elements while the predicate is true, then returns the rest.
	 *
	 * @param Closure(E, int):bool $predicate
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function dropWhile(Closure $predicate): Sequence;

	/**
	 * Distinct elements by identity.
	 * Elements already seen during the current pass are skipped, so the memory held grows
	 * with the number of distinct elements.
	 *
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function distinct(): Sequence;

	/**
	 * Distinct elements by selector.
	 *
	 * @template K
	 * @param Closure(E, int):K $selector
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function distinctBy(Closure $selector): Sequence;

	/**
	 * Combines this sequence with another iterable by pairing elements at the same position.
	 * The resulting sequence has the length of the shorter input.
	 *
	 * The other side is pulled in lockstep rather than copied, and rewound first just as
	 * foreach would rewind it: an already started Generator therefore throws. Wrap it in a
	 * NoRewindIterator to deliberately resume a cursor that is already in flight.
	 *
	 * @template U
	 * @param iterable<U> $other
	 * @return Sequence<array{E, U}>
	 */
	#[NoDiscard]
	public function zip(iterable $other): Sequence;

	/**
	 * Returns a sequence of pairs of each two adjacent elements in this sequence.
	 * If the sequence has fewer than two elements, yields nothing.
	 *
	 * @return Sequence<array{E, E}>
	 */
	#[NoDiscard]
	public function zipWithNext(): Sequence;

	// --- Iteration ---

	/**
	 * Returns a sequence applying the given action to each element as it goes through,
	 * then yielding the element unchanged.
	 *
	 * The lazy, chainable counterpart of forEach: the action runs once per element and per
	 * pass, while the element flows through the pipeline - nothing happens before a
	 * terminal operation pulls.
	 *
	 * @param Closure(E, int):void $action
	 * @return Sequence<E>
	 */
	#[NoDiscard]
	public function onEach(Closure $action): Sequence;

	// --- Conversion ---

	/**
	 * Convert to an immutable list, consuming one pass of the sequence.
	 *
	 * @return ImmutableList<E>
	 */
	#[NoDiscard]
	public function toList(): ImmutableList;

	/**
	 * Convert to an immutable set (duplicates removed), consuming one pass of the sequence.
	 *
	 * @return ImmutableSet<E>
	 */
	#[NoDiscard]
	public function toSet(): ImmutableSet;

	/**
	 * Convert to a primitive PHP array, consuming one pass of the sequence.
	 *
	 * @return list<E>
	 */
	#[NoDiscard]
	public function toArray(): array;
}
