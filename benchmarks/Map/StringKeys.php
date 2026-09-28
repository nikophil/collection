<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Map;

use Noctud\Collection\Benchmarks\Fixture\Data;

trait StringKeys
{
	/**
	 * @return list<string>
	 */
	protected function keysOf(int $size, int $offset = 0): array
	{
		return Data::strings($size, $offset);
	}
}
