<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Fixture;

/**
 * Plain object element, hashed by object identity (spl_object_id).
 */
final readonly class Item
{
	public function __construct(
		public int $id,
	) {
	}
}
