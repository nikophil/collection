<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type;

use function Noctud\Collection\listOf;
use function PHPStan\Testing\assertType;

// A collection of ints sums to an int.
assertType('int', listOf([1, 2, 3])->sum());

// As soon as a float is involved, the sum widens back to int|float.
assertType('float|int', listOf([1.0, 2.0])->sum());
assertType('float|int', listOf([1, 2.0])->sum());

// With a selector declared to return int, the sum narrows to int.
assertType('int', listOf([1, 2, 3])->sum(fn (int $x): int => $x * 2));
// Any other selector return type (float, or an un-inferrable/mixed one) stays int|float.
assertType('float|int', listOf([1, 2, 3])->sum(fn (int $x): float => $x / 2));
