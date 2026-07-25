<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Map;

use function Noctud\Collection\mapOf;
use function Noctud\Collection\mutableMapOf;
use function PHPStan\Testing\assertType;

$imm = mapOf(['a' => 1, 'b' => 2]);

// Clean int/bool values derived from the map itself, to observe widening.
$anInt = $imm->get('a');
$aBool = $imm->isEmpty();

// Immutable put/putFirst/putIfAbsent WIDEN both key and value types.
assertType('Noctud\Collection\Map\ImmutableMap<int|string, bool|int>', $imm->put($anInt, $aBool));
assertType('Noctud\Collection\Map\ImmutableMap<int|string, bool|int>', $imm->putIfAbsent($anInt, $aBool));
assertType('Noctud\Collection\Map\ImmutableMap<int|string, bool|int>', $imm->putFirst($anInt, $aBool));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->putAll(['c' => 3]));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->putAllPairs([['c', 3]]));

// Removals keep both key and value types.
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->remove('a'));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->removeFirst());
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->removeLast());
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->removeIf(fn (int $v, string $k): bool => $v > 0));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->removeIfKey(fn (string $k): bool => $k !== ''));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->removeIfValue(fn (int $v): bool => $v > 0));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $imm->removeNullValues());

// Mutable put/remove are STRICT and return the same MutableMap.
$mut = mutableMapOf(['a' => 1]);
assertType('int', $mut->getOrPut('a', fn (): int => 0));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->put('b', 2));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->putIfAbsent('c', 3));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->putFirst('z', 0));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->putAll(['d' => 4]));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->remove('a'));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->clear());
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->sortByKey());
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->sortByValue());
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->sortBy(fn (int $v, string $k): int => $v));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->sortWithKey(fn (string $a, string $b): int => $a <=> $b));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->sortWithValue(fn (int $a, int $b): int => $a <=> $b));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->sortWith(fn ($a, $b): int => 0));
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->reverse());
assertType('Noctud\Collection\Map\MutableMap<string, int>', $mut->shuffle());
