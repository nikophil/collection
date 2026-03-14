<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\List\ArrayList;

use ArrayIterator;
use Noctud\Collection\Exception\IndexOutOfBoundsException;
use Noctud\Collection\Store\AbstractElementStore;
use Noctud\Collection\Store\ReadWriteIndexedStore;
use Traversable;

/**
 * Internal storage for ArrayList.
 *
 * @property-read array<non-negative-int,E> $elements
 * @template E
 * @extends AbstractElementStore<E>
 * @implements ReadWriteIndexedStore<E>
 */
final class ArrayIndexStore extends AbstractElementStore implements ReadWriteIndexedStore
{
	/**
	 * @param iterable<E> $source
	 */
	public function __construct(iterable $source = [])
	{
		if (is_array($source)) {
			$this->elements = array_is_list($source) ? $source : array_values($source);
		} else {
			$this->addAll($source);
		}
	}

	public function addAll(iterable $source): void
	{
		if (is_array($source)) {
			array_push($this->elements, ...array_is_list($source) ? $source : array_values($source));
		} else {
			foreach ($source as $element) {
				$this->elements[] = $element;
			}
		}
	}

	/**
	 * @param E $element
	 */
	public function add(mixed $element): void
	{
		$this->elements[] = $element;
	}

	/**
	 * @param E $element
	 */
	public function addFirst(mixed $element): void
	{
		array_unshift($this->elements, $element);
	}

	/**
	 * @param int $index
	 * @return E|null
	 */
	public function get(int $index, bool $throw = false): mixed
	{
		if (!array_key_exists($index, $this->elements)) {
			if ($throw) {
				throw new IndexOutOfBoundsException('Index out of bounds: ' . $index);
			} else {
				return null;
			}
		}

		return $this->elements[$index] ?? null;
	}

	public function indexOf(mixed $value): int
	{
		/** @var int $i */
		foreach ($this->elements as $i => $v) {
			if ($v === $value) {
				return $i;
			}
		}

		return -1;
	}

	public function lastIndexOf(mixed $value): int
	{
		for ($i = count($this->elements) - 1; $i >= 0; $i--) {
			if ($this->elements[$i] === $value) {
				return $i;
			}
		}

		return -1;
	}

	public function set(int $index, mixed $element): void
	{
		if (!array_key_exists($index, $this->elements)) {
			throw new IndexOutOfBoundsException('Index out of bounds: ' . $index);
		}

		$this->elements[$index] = $element;
	}

	public function removeAt(int $index): void
	{
		if (!array_key_exists($index, $this->elements)) {
			throw new IndexOutOfBoundsException('Index out of bounds: ' . $index);
		}

		array_splice($this->elements, $index, 1);
	}

	public function removeFirstOccurrence(mixed $element): void
	{
		/** @var int $k */
		foreach ($this->elements as $k => $v) {
			if ($v === $element) {
				array_splice($this->elements, $k, 1);
				return;
			}
		}
	}

	public function removeEvery(mixed $element): void
	{
		for ($k = count($this->elements) - 1; $k >= 0; $k--) {
			if ($this->elements[$k] === $element) {
				array_splice($this->elements, $k, 1);
			}
		}
	}

	public function contains(mixed $element): bool
	{
		return in_array($element, $this->elements, true);
	}

	public function toArray(): array
	{
		return $this->elements;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->elements); // @phpstan-ignore return.type
	}
}
