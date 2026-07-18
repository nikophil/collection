<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Map;

use stdClass;
use function Noctud\Collection\intMapOf;
use function Noctud\Collection\listOf;
use function Noctud\Collection\mapOf;
use function PHPStan\Testing\assertType;

$map = mapOf(['a' => 1, 'b' => 2]);

// toArray is conditional: a string|int key stays a native array key.
assertType('array<string, int>', $map->toArray());
assertType('array<int, string>', intMapOf([1 => 'a', 2 => 'b'])->toArray());
// Non-array-key keys (e.g. objects) fall back to a value-typed array.
assertType('array<string>', listOf(['a'])->toMap(fn (string $i): stdClass => new stdClass())->toArray());

// map/mapNotNull/flatMap flatten the map down to an immutable list.
assertType('Noctud\Collection\List\ImmutableList<bool>', $map->map(fn (int $v, string $k): bool => $v > 0));
assertType('Noctud\Collection\List\ImmutableList<bool>', $map->mapNotNull(fn (int $v, string $k): ?bool => $v > 0 ? true : null));
assertType('Noctud\Collection\List\ImmutableList<int>', $map->flatMap(fn (int $v, string $k): array => [$v]));

assertType('list<array{string, int}>', $map->toPairs());
assertType('Noctud\Collection\Map\MutableMap<string, int>', $map->toMutable());
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $map->toImmutable());

// forEach variants return the map unchanged.
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $map->forEach(fn (int $v, string $k): null => null));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $map->forEachKey(fn (string $k): null => null));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', $map->forEachValue(fn (int $v): null => null));
