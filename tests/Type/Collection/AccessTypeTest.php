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

assertType('int', $c->first());
assertType('int|null', $c->firstOrNull());
assertType('int', $c->last());
assertType('int|null', $c->lastOrNull());
assertType('int', $c->single());
assertType('int|null', $c->singleOrNull());
assertType('int', $c->elementAt(1));
assertType('int|null', $c->elementAtOrNull(1));
// The OrNull variant takes any int: a negative index is an absence, not a misuse.
assertType('int|null', $c->elementAtOrNull(-1));
assertType('int|null', $c->find(fn (int $x): bool => $x > 0));
assertType('int|null', $c->findLast(fn (int $x): bool => $x > 0));
assertType('int', $c->expect(fn (int $x): bool => $x > 0));
assertType('int', $c->expectLast(fn (int $x): bool => $x > 0));
assertType('int', $c->random());
assertType('int|null', $c->randomOrNull());
