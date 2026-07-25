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
use function Noctud\Collection\setOf;
use function PHPStan\Testing\assertType;

// flatten() extracts the element type of iterable elements (one level).
assertType('Noctud\Collection\List\ImmutableList<int>', listOf([listOf([1, 2]), listOf([3])])->flatten());
assertType('Noctud\Collection\Set\ImmutableSet<int>', setOf([listOf([1, 2]), listOf([3])])->flatten());
assertType('Noctud\Collection\List\ImmutableList<int>', mutableListOf([listOf([1, 2])])->flatten());

// Only one level is flattened.
assertType(
	'Noctud\Collection\List\ImmutableList<Noctud\Collection\List\ImmutableList<int>>',
	listOf([listOf([listOf([1])])])->flatten(),
);

// Plain arrays are iterables too.
/** @var Collection<array<int>> $arrays */
$arrays = listOf([[1, 2], [3]]);
assertType('Noctud\Collection\Collection<int>', $arrays->flatten());

// Non-iterable elements are kept as-is...
assertType('Noctud\Collection\List\ImmutableList<string>', listOf(['a', 'b'])->flatten());

// ...and the conditional distributes over union element types.
/** @var Collection<array<int>|string> $mixed */
$mixed = listOf([[1], 'a']);
assertType('Noctud\Collection\Collection<int|string>', $mixed->flatten());

// flatten() on an empty collection stays typed (E = never).
assertType('Noctud\Collection\List\ImmutableList<*NEVER*>', listOf([])->flatten());
