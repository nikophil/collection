<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection\List\Extending;

use Closure;
use Noctud\Collection\List\ArrayList\ArrayIndexStore;
use Noctud\Collection\List\ImmutableList;
use Noctud\Collection\List\ImmutableListLogic;
use Noctud\Collection\Tests\Collection\Set\Extending\SwappableItem;

/**
 * Extension style #2 (List): base ImmutableListLogic + class-level `@method self`
 * overrides + a newCollectionOf override.
 *
 * @implements ImmutableList<SwappableItem>
 * @phpstan-consistent-constructor
 * @method self filter(Closure(SwappableItem, int): bool $predicate)
 * @method self sorted()
 */
class ManualLineItems implements ImmutableList
{
	/** @use ImmutableListLogic<SwappableItem> */
	use ImmutableListLogic;

	/** @param iterable<SwappableItem> $data */
	public function __construct(iterable $data = [])
	{
		$this->store = new ArrayIndexStore($data);
	}

	/** @param iterable<SwappableItem> $data */
	protected function newCollectionOf(iterable $data): ImmutableList
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
}
