<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Collection;

use Noctud\Collection\Collection;
use function Noctud\Collection\listOf;
use function PHPStan\Testing\assertType;

/** @var Collection<int> $c */
$c = listOf([1, 2, 3]);

// fold: return type follows the initial accumulator type R.
assertType('int', $c->fold(0, fn (int $acc, int $x): int => $acc + $x));
assertType('string', $c->fold('', fn (string $acc, int $x): string => $acc . $x));

assertType('int', $c->reduce(fn (int $acc, int $x): int => $acc + $x));
assertType('int|null', $c->reduceOrNull(fn (int $acc, int $x): int => $acc + $x));

// min/max return the element type E (declared `: mixed`), not a fixed scalar.
assertType('int', $c->min());
assertType('int|null', $c->minOrNull());
assertType('int', $c->max());
assertType('int|null', $c->maxOrNull());

// minOf/maxOf return the selector's type R instead of mixed.
$strings = listOf(['a', 'bb']);
assertType('int', $strings->minOf(fn (string $s): int => (int) $s));
assertType('int', $strings->maxOf(fn (string $s): int => (int) $s));
assertType('float', $strings->maxOf(fn (string $s): float => (float) $s));

// The OrNull variants add null for the empty-collection case.
assertType('int|null', $strings->minOfOrNull(fn (string $s): int => (int) $s));
assertType('int|null', $strings->maxOfOrNull(fn (string $s): int => (int) $s));

// R is inferred even from an untyped selector parameter.
assertType('int', listOf([1, 2, 3])->minOf(fn ($x) => (int) $x));

// sum: a collection of ints sums to an int.
assertType('int', listOf([1, 2, 3])->sum());

// As soon as a float is involved, the sum widens back to int|float.
assertType('float|int', listOf([1.0, 2.0])->sum());
assertType('float|int', listOf([1, 2.0])->sum());

// With a selector declared to return int, the sum narrows to int.
assertType('int', listOf([1, 2, 3])->sum(fn (int $x): int => $x * 2));
// Any other selector return type (float, or an un-inferrable/mixed one) stays int|float.
assertType('float|int', listOf([1, 2, 3])->sum(fn (int $x): float => $x / 2));
