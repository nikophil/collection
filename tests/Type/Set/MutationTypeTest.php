<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Set;

use function Noctud\Collection\mutableSetOf;
use function Noctud\Collection\setOf;
use function PHPStan\Testing\assertType;

$imm = setOf([1, 2, 3]);

// Immutable mutators WIDEN the element type.
assertType('Noctud\Collection\Set\ImmutableSet<int|string>', $imm->add('x'));
assertType('Noctud\Collection\Set\ImmutableSet<int|string>', $imm->addFirst('x'));
assertType('Noctud\Collection\Set\ImmutableSet<int|string>', $imm->addAll(['x']));

// Removals keep the element type.
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->removeIf(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->removeAll([1]));
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->removeElement(1));
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->removeFirst());
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->removeLast());
assertType('Noctud\Collection\Set\ImmutableSet<int>', $imm->retainAll([1]));

// Mutable mutators are STRICT (@param E) and return the same MutableSet.
$mut = mutableSetOf([1, 2, 3]);
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->add(4));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->addFirst(0));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->addAll([4, 5]));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->removeElement(1));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->removeIf(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->clear());
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->sort());
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->sortBy(fn (int $x): int => $x));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->sortWith(fn (int $a, int $b): int => $a <=> $b));
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->reverse());
assertType('Noctud\Collection\Set\MutableSet<int>', $mut->shuffle());
