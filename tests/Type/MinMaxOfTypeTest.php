<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type;

use function Noctud\Collection\listOf;
use function PHPStan\Testing\assertType;

$strings = listOf(['a', 'bb']);

// minOf/maxOf return the selector's type R instead of mixed.
assertType('int', $strings->minOf(fn (string $s): int => (int) $s));
assertType('int', $strings->maxOf(fn (string $s): int => (int) $s));
assertType('float', $strings->maxOf(fn (string $s): float => (float) $s));

// The OrNull variants add null for the empty-collection case.
assertType('int|null', $strings->minOfOrNull(fn (string $s): int => (int) $s));
assertType('int|null', $strings->maxOfOrNull(fn (string $s): int => (int) $s));

// R is inferred even from an untyped selector parameter.
assertType('int', listOf([1, 2, 3])->minOf(fn ($x) => (int) $x));
