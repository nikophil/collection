<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Fixture;

use Generator;

trait ProvidesSizes
{
	/**
	 * A small size exposes the fixed overhead (object creation, closures, store setup),
	 * a large one the per-element cost.
	 *
	 * @return Generator<string, array{size: positive-int}>
	 */
	public function provideSizes(): Generator
	{
		yield '100' => ['size' => 100];
		yield '1k' => ['size' => 1_000];
	}
}
