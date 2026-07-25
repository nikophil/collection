<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\List;

use function Noctud\Collection\listOf;
use function Noctud\Collection\mutableListOf;
use function PHPStan\Testing\assertType;

$imm = listOf([1, 2, 3]);

// Immutable mutators WIDEN the element type.
assertType('Noctud\Collection\List\ImmutableList<int|string>', $imm->add('x'));
assertType('Noctud\Collection\List\ImmutableList<int|string>', $imm->addFirst('x'));
assertType('Noctud\Collection\List\ImmutableList<int|string>', $imm->addAll(['x']));
assertType('Noctud\Collection\List\ImmutableList<int|string>', $imm->set(0, 'x'));

// Removals keep the element type.
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeEvery(1));
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeAt(0));
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeIf(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeAll([1]));
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeElement(1));
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeFirst());
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->removeLast());
assertType('Noctud\Collection\List\ImmutableList<int>', $imm->retainAll([1]));

// Mutable mutators are STRICT (@param E) and return the same MutableList.
$mut = mutableListOf([1, 2, 3]);
assertType('Noctud\Collection\List\MutableList<int>', $mut->add(4));
assertType('Noctud\Collection\List\MutableList<int>', $mut->addFirst(0));
assertType('Noctud\Collection\List\MutableList<int>', $mut->addAll([4, 5]));
assertType('Noctud\Collection\List\MutableList<int>', $mut->set(0, 9));
assertType('Noctud\Collection\List\MutableList<int>', $mut->removeEvery(1));
assertType('Noctud\Collection\List\MutableList<int>', $mut->removeAt(0));
assertType('Noctud\Collection\List\MutableList<int>', $mut->removeIf(fn (int $x): bool => $x > 0));
assertType('Noctud\Collection\List\MutableList<int>', $mut->clear());
assertType('Noctud\Collection\List\MutableList<int>', $mut->sort());
assertType('Noctud\Collection\List\MutableList<int>', $mut->sortBy(fn (int $x): int => $x));
assertType('Noctud\Collection\List\MutableList<int>', $mut->sortWith(fn (int $a, int $b): int => $a <=> $b));
assertType('Noctud\Collection\List\MutableList<int>', $mut->reverse());
assertType('Noctud\Collection\List\MutableList<int>', $mut->shuffle());
