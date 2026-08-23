<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Sequence;

use Noctud\Collection\Sequence\Sequence;
use function Noctud\Collection\sequenceOf;
use function PHPStan\Testing\assertType;

/** @var Sequence<string> $s */
$s = sequenceOf(['a', 'b', 'c']);

// count() and countWhere() pin their declared @return int<0, max>; the rest of the querying
// family is native `: bool`, so nothing else here needs a type test.
assertType('int<0, max>', $s->count());
assertType('int<0, max>', $s->countWhere(fn (string $v): bool => $v !== ''));
