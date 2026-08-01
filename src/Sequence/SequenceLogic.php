<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Sequence;

use Closure;
use Generator;
use IteratorAggregate;
use Noctud\Collection\Exception\InvalidSequenceSourceException;
use Noctud\Collection\Exception\NonReplayableSourceException;
use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\Operation\DistinctOperation;
use Noctud\Collection\Operation\DropOperation;
use Noctud\Collection\Operation\FilterOperation;
use Noctud\Collection\Operation\FlatMapKeyValueOperation;
use Noctud\Collection\Operation\FlattenOperation;
use Noctud\Collection\Operation\MapKeyValueOperation;
use Noctud\Collection\Operation\TakeOperation;
use Noctud\Collection\Operation\ZipOperation;
use Noctud\Collection\Operation\ZipWithNextOperation;
use Noctud\Collection\Set\ImmutableSet;
use NoDiscard;
use Traversable;
use WeakReference;
use function Noctud\Collection\listOf;
use function Noctud\Collection\setOf;

/**
 * @template E
 * @mixin Sequence<E>
 */
trait SequenceLogic
{
	/** @var iterable<E>|Closure():iterable<E> */
	private iterable|Closure $source;

	/** Whether a single-pass source has already been handed out. */
	private bool $consumed = false;

	/**
	 * Iterator the source produced on the previous pass, compared by identity in
	 * resolveProducedIterable(). Held weakly so a consumed iterator - and the file handle or
	 * cursor behind it - is freed as soon as nothing else uses it: a collected one leaves
	 * get() returning null, and an iterator nobody holds cannot be handed back to us.
	 *
	 * @var WeakReference<Traversable>|null
	 */
	private ?WeakReference $lastProduced = null;

	/**
	 * @return Generator<int, E>
	 */
	public function getIterator(): Generator
	{
		$iterable = $this->resolveSourceForThisPass();

		return (static function () use ($iterable): Generator {
			$i = 0;
			foreach ($iterable as $value) {
				yield $i++ => $value;
			}
		})();
	}

	// --- Transformation ---

	/** {@inheritDoc} */
	#[NoDiscard]
	public function filter(Closure $predicate): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new FilterOperation($this)->byPredicate($predicate));
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return Sequence<(E is null ? never : E)>
	 */
	#[NoDiscard] // @phpstan-ignore conditionalType.subjectNotFound (in classes with a concrete E the conditional subject is already substituted)
	public function filterNotNull(): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new FilterOperation($this)->notNullValues());
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function filterInstanceOf(string $type): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new FilterOperation($this)->byValue(fn ($v) => $v instanceof $type)); // @phpstan-ignore return.type
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function map(Closure $transform): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new MapKeyValueOperation($this)->items($transform));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function mapNotNull(Closure $transform): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new MapKeyValueOperation($this)->itemsNotNull($transform));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function flatMap(Closure $transform): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new FlatMapKeyValueOperation($this)->items($transform));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function flatten(): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new FlattenOperation($this)->items());
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function takeFirst(int $n = 1): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new TakeOperation($this)->first($n));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function dropFirst(int $n = 1): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new DropOperation($this)->first($n));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function takeWhile(Closure $predicate): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new TakeOperation($this)->byPredicate($predicate));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function dropWhile(Closure $predicate): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new DropOperation($this)->byPredicate($predicate));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function distinct(): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new DistinctOperation($this)->items());
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function distinctBy(Closure $selector): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new DistinctOperation($this)->bySelector($selector));
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function zip(iterable $other): Sequence
	{
		// Deciding whether the other side can be walked again belongs next to the cursor() that
		// rewinds it, or the two classifications drift apart; remembering that a pass already
		// walked it is sequence-only state and stays here.
		$otherIsReplayable = ZipOperation::isReplayable($other);
		$otherConsumed = false;

		return $this->newSequenceOf(function () use ($other, $otherIsReplayable, &$otherConsumed): iterable {
			if (!$otherIsReplayable && $otherConsumed) {
				throw NonReplayableSourceException::zippedIterableAlreadyIterated();
			}

			// Marked per pair rather than per pass: a pass that pairs nothing never positions
			// the other side, which stays where it was and replayable - so it must not be
			// counted as consumed.
			return (function () use ($other, &$otherConsumed): Generator {
				foreach (new ZipOperation($this)->with($other) as $pair) {
					$otherConsumed = true;

					yield $pair;
				}
			})();
		});
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function zipWithNext(): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new ZipWithNextOperation($this)->pairs());
	}

	// --- Iteration ---

	/** {@inheritDoc} */
	#[NoDiscard]
	public function onEach(Closure $action): Sequence
	{
		return $this->newSequenceOf(fn (): iterable => new MapKeyValueOperation($this)->items(function ($v, $k) use ($action) {
			$action($v, $k);

			return $v;
		}));
	}

	// --- Conversion ---

	/** {@inheritDoc} */
	#[NoDiscard]
	public function toList(): ImmutableList
	{
		return listOf($this);
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function toSet(): ImmutableSet
	{
		return setOf($this);
	}

	/** {@inheritDoc} */
	#[NoDiscard]
	public function toArray(): array
	{
		return iterator_to_array($this, false);
	}

	/**
	 * Chains a lazy stage. The factory is invoked once per pass (never at chain-build
	 * time), so every pass runs a fresh Operation generator and replayability follows
	 * the root source.
	 *
	 * @template R
	 * @param Closure():iterable<R> $factory
	 * @return Sequence<R>
	 */
	protected function newSequenceOf(Closure $factory): Sequence
	{
		return new GeneratorSequence($factory);
	}

	/**
	 * Resolves the source for one pass, enforcing the replayability contract: an array
	 * replays freely, a Closure and an IteratorAggregate are both producers asked for an
	 * iterable once per pass (and must hand back a fresh one each time), and any other
	 * Traversable is single-pass - even when technically rewindable, matching Kotlin's
	 * Iterator.asSequence(). The guard runs here, at getIterator() call time, so a
	 * violation throws at the start of the offending pass instead of silently yielding
	 * nothing.
	 *
	 * @return iterable<E>
	 */
	private function resolveSourceForThisPass(): iterable
	{
		$source = $this->source;

		if ($source instanceof Closure) {
			$produced = $source();

			// @phpstan-ignore function.alreadyNarrowedType (the closure's return type is a PHPDoc promise PHP cannot enforce)
			if (!is_iterable($produced)) {
				throw InvalidSequenceSourceException::closureReturnedNonIterable($produced);
			}

			return $this->resolveProducedIterable($produced);
		}

		if (is_array($source)) {
			return $source;
		}

		if ($source instanceof IteratorAggregate) {
			return $this->resolveProducedIterable($source);
		}

		if ($this->consumed) {
			throw NonReplayableSourceException::sequenceSourceAlreadyIterated();
		}

		$this->consumed = true;

		return $source;
	}

	/**
	 * Rejects a producer - a source Closure or an IteratorAggregate - handing back the
	 * cursor of the previous pass: implementing IteratorAggregate promises nothing about
	 * replayability, getIterator() is free to return a Generator the object keeps around,
	 * and traversing that one again would yield nothing (or leak a raw PHP error).
	 *
	 * @param iterable<E> $produced
	 * @return iterable<E>
	 */
	private function resolveProducedIterable(iterable $produced): iterable
	{
		if ($produced instanceof IteratorAggregate) {
			$produced = $produced->getIterator();
		}

		// Cursors only: an array is a value, always replayable, and a nested aggregate is itself
		// a producer, re-invoked per pass by the foreach that unwraps it.
		if ($produced instanceof Traversable && !$produced instanceof IteratorAggregate) {
			if ($this->lastProduced?->get() === $produced) {
				throw NonReplayableSourceException::sourceReturnedSameIterator();
			}

			$this->lastProduced = WeakReference::create($produced);
		}

		return $produced;
	}
}
