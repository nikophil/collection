<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Map;

use function Noctud\Collection\mapOf;
use function PHPStan\Testing\assertType;

$map = mapOf(['a' => 1, 'b' => 2]);

// countWhere() pins the declared @return int<0, max>.
// count() itself is native-typed (`: int`, no PHPDoc) and needs no type test.
assertType('int<0, max>', $map->countWhere(fn (int $v, string $k): bool => $v > 0));
