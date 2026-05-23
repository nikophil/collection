<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection\Set\Extending;

use Closure;
use Noctud\Collection\Set\HashSet\HashElementStore;
use Noctud\Collection\Set\ImmutableSet;
use Noctud\Collection\Set\ImmutableSetLogic;

/**
 * Extension style #2: the base ImmutableSetLogic with hand-written class-level
 * `@method self` overrides + a newCollectionOf override. Param types use the
 * concrete element type, so element/closure checking is fully preserved.
 *
 * @implements ImmutableSet<SwappableItem>
 * @phpstan-consistent-constructor
 * @method self filter(Closure(SwappableItem, int): bool $predicate)
 * @method self sorted()
 * @method array{self, self} partition(Closure(SwappableItem, int): bool $predicate)
 */
class ManualItemCollection implements ImmutableSet
{
	/** @use ImmutableSetLogic<SwappableItem> */
	use ImmutableSetLogic;

	/** @param iterable<SwappableItem> $data */
	public function __construct(iterable $data = [])
	{
		$this->store = new HashElementStore($data);
	}

	/** @param iterable<SwappableItem> $data */
	protected function newCollectionOf(iterable $data): ImmutableSet
	{
		return new self($data);
	}

	public function onlySwapped(): self
	{
		return $this->filter(static fn (SwappableItem $i): bool => $i->swapped);
	}

	public function sortedItems(): self
	{
		return $this->sorted();
	}

	/** @return array{self, self} */
	public function splitBySwapped(): array
	{
		return $this->partition(static fn (SwappableItem $i): bool => $i->swapped);
	}
}
