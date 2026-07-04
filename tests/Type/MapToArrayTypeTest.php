<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type;

use stdClass;
use function Noctud\Collection\listOf;
use function PHPStan\Testing\assertType;

assertType(
	'array<string, string>',
	listOf(['a', 'b', 'c'])->toMap(fn (string $i) => $i)->toArray(),
);
assertType(
	'array<int, string>',
	listOf(['a', 'b'])->toMap(fn (string $i): int => 0)->toArray(),
);

assertType(
	'array<int, int>',
	listOf(['a', 'b'])->toMap(fn (string $i): int => 0, fn (string $i): int => 1)->toArray(),
);

assertType(
	'array<string>',
	listOf(['a'])->toMap(fn (string $i) => new stdClass())->toArray(),
);
