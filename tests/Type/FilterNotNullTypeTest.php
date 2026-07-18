<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type;

use Noctud\Collection\Collection;

use function Noctud\Collection\listOf;
use function Noctud\Collection\mutableListOf;
use function Noctud\Collection\mutableSetOf;
use function Noctud\Collection\mutableStringMapOf;
use function Noctud\Collection\setOf;
use function Noctud\Collection\stringMapOf;
use function PHPStan\Testing\assertType;

// filterNotNull() removes null from the element type (issue #16).
assertType('Noctud\Collection\List\ImmutableList<int>', listOf([1, null])->filterNotNull());
assertType('Noctud\Collection\List\ImmutableList<int>', mutableListOf([1, null])->filterNotNull());
assertType('Noctud\Collection\Set\ImmutableSet<int>', setOf([1, null])->filterNotNull());
assertType('Noctud\Collection\Set\ImmutableSet<int>', mutableSetOf([1, null])->filterNotNull());

// Also through the base Collection interface.
/** @var Collection<string|null> $c */
$c = listOf(['a', null]);
assertType('Noctud\Collection\Collection<string>', $c->filterNotNull());

// A collection without null keeps its element type unchanged.
assertType('Noctud\Collection\List\ImmutableList<int>', listOf([1, 2])->filterNotNull());

// filterValuesNotNull() removes null from the value type, keys are preserved.
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', stringMapOf(['a' => 1, 'b' => null])->filterValuesNotNull());
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', mutableStringMapOf(['a' => 1, 'b' => null])->filterValuesNotNull());
