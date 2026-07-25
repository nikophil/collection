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

assertType('int', $map->get('a'));
assertType('int|null', $map->getOrNull('a'));
assertType("'x'|int", $map->getOrDefault('a', 'x'));
assertType("'x'|int", $map->getOrCompute('a', fn (): string => 'x'));
// Array access via [] returns the value (offsetGet throws on a missing key).
assertType('int', $map['a']);

// Virtual property hooks expose the key/value/entry views.
assertType('Noctud\Collection\Set\Set<string>', $map->keys);
assertType('Noctud\Collection\Collection<int>', $map->values);
assertType('Noctud\Collection\Set\Set<Noctud\Collection\Map\MapEntry<string, int>>', $map->entries);
