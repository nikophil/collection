<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Fixture;

use Noctud\Collection\Hashable;

/**
 * Object element hashed by value through Hashable::identity().
 */
final readonly class HashableItem implements Hashable
{
	public function __construct(
		public int $id,
	) {
	}

	public function identity(): string
	{
		return "item:{$this->id}";
	}
}
