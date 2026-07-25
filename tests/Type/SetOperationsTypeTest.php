<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type;

use Noctud\Collection\Collection;
use function Noctud\Collection\listOf;
use function Noctud\Collection\setOf;
use function PHPStan\Testing\assertType;

/** @var Collection<string> $c */
$c = listOf(['a', 'b', 'c']);
/** @var Collection<int|string> $scalars */
$scalars = listOf([1, 'a']);
/** @var iterable<int> $ints */
$ints = [1, 2];
/** @var iterable<mixed> $anything */
$anything = [1, 'a'];

// With a same-typed iterable, the element type is unchanged.
assertType('Noctud\Collection\Set\Set<string>', $c->intersect(['a', 'b']));
assertType('Noctud\Collection\Set\Set<string>', $c->union(['a', 'b']));
assertType('Noctud\Collection\Set\Set<string>', $c->subtract(['a', 'b']));

// intersect narrows to E&V: only values that can belong to both sides.
assertType('Noctud\Collection\Set\Set<int>', $scalars->intersect($ints));
assertType('Noctud\Collection\Set\Set<string>', $c->intersect($anything));
// Intersecting disjoint types is reported as an unresolvable return type.
assertType('Noctud\Collection\Set\Set<*NEVER*>', $c->intersect($ints)); // @phpstan-ignore method.unresolvableReturnType

// union widens to E|V: elements of both sides end up in the result.
assertType('Noctud\Collection\Set\Set<int|string>', $c->union($ints));
assertType('Noctud\Collection\Set\Set<int|string>', $scalars->union($ints));

// subtract always keeps E: any iterable may be subtracted.
assertType('Noctud\Collection\Set\Set<int|string>', $scalars->subtract($ints));
assertType('Noctud\Collection\Set\Set<string>', $c->subtract($anything));

// Same behavior on a Set receiver.
$imm = setOf([1, 2, 3]);
/** @var iterable<string> $strings */
$strings = ['a', 'b'];
assertType('Noctud\Collection\Set\Set<*NEVER*>', $imm->intersect($strings)); // @phpstan-ignore method.unresolvableReturnType
assertType('Noctud\Collection\Set\Set<int|string>', $imm->union($strings));
assertType('Noctud\Collection\Set\Set<int>', $imm->subtract($strings));
