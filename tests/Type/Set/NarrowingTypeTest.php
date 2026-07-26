<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Set;

use Noctud\Collection\Set\Set;
use stdClass;
use function Noctud\Collection\listOf;
use function Noctud\Collection\mutableSetOf;
use function Noctud\Collection\setOf;
use function PHPStan\Testing\assertType;

$imm = setOf([1, 2, 3]);

// Shape-preserving transforms narrow back to ImmutableSet.
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->filter(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->filterNotNull());
assertType('Noctud\Collection\Set\ImmutableSet<int>', setOf([1, null])->filterNotNull());
assertType('Noctud\Collection\Set\ImmutableSet<int>', mutableSetOf([1, null])->filterNotNull());
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->takeFirst(2));
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->distinct());
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->sorted());
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->reversed());
assertType('Noctud\Collection\Set\Set<int>', $imm->intersect([1, 2]));
assertType('Noctud\Collection\Set\Set<int>', $imm->union([1, 2]));
assertType('Noctud\Collection\Set\Set<int>', $imm->subtract([1, 2]));
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->toImmutable());
assertType(
	'array{Noctud\Collection\Set\ImmutableSet<int>, Noctud\Collection\Set\ImmutableSet<int>}',
	$imm->partition(fn (int $x): bool => $x > 0),
);

// Type-changing transforms move to the new element type but stay ImmutableSet.
assertType('Noctud\Collection\Set\ImmutableSet<bool>', $imm->map(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\Set\ImmutableSet<stdClass>', $imm->filterInstanceOf(stdClass::class));

// flatten() extracts the element type of iterable elements (one level).
assertType('Noctud\Collection\Set\ImmutableSet<int>', setOf([listOf([1, 2]), listOf([3])])->flatten());

// Cross-typed set operations follow the same E&V / E|V / E rules as on Collection.
/** @var iterable<string> $strings */
$strings = ['a', 'b'];
assertType('Noctud\Collection\Set\Set<*NEVER*>', $imm->intersect($strings)); // @phpstan-ignore method.unresolvableReturnType
assertType('Noctud\Collection\Set\Set<int|string>', $imm->union($strings));
assertType('Noctud\Collection\Set\Set<int>', $imm->subtract($strings));

// The base Set contract keeps everything at the Set interface level.
/** @var Set<int> $set */
$set = setOf([1, 2, 3]);
assertType('Noctud\Collection\Set\Set<int>', $set->filter(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\Set\Set<bool>', $set->map(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\Set\Set<int>', $set->sorted());
