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

// countWhere() pins the declared @return int<0, max>.
// count() itself is native-typed (`: int`, no PHPDoc), as is the rest of the querying family
// (`: bool`), so nothing else here needs a type test.
assertType('int<0, max>', $s->countWhere(fn (string $v): bool => $v !== ''));
