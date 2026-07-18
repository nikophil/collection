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

assertType('Noctud\Collection\Collection<int>', $c->sorted());
assertType('Noctud\Collection\Collection<int>', $c->sortedDesc());
assertType('Noctud\Collection\Collection<int>', $c->sortedBy(fn (int $x): int => $x));
assertType('Noctud\Collection\Collection<int>', $c->sortedByDesc(fn (int $x): int => $x));
assertType('Noctud\Collection\Collection<int>', $c->sortedWith(fn (int $a, int $b): int => $a <=> $b));
assertType('Noctud\Collection\Collection<int>', $c->reversed());
assertType('Noctud\Collection\Collection<int>', $c->shuffled());
