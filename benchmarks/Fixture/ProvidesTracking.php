<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Fixture;

use Generator;

trait ProvidesTracking
{
	/**
	 * Runs mutations on the collection itself and through its tracked() view,
	 * which also records whether each call changed something.
	 *
	 * @return Generator<string, array{tracked: bool}>
	 */
	public function provideTracking(): Generator
	{
		yield 'plain' => ['tracked' => false];
		yield 'tracked' => ['tracked' => true];
	}
}
