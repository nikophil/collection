<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Type\Map;

use Noctud\Collection\Map\HashMap\ImmutableHashMap;
use Noctud\Collection\Map\HashMap\MutableHashMap;
use function PHPStan\Testing\assertType;

// Static factories infer key/value types from the given data.
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', ImmutableHashMap::of(['a' => 1, 'b' => 2]));
assertType('Noctud\Collection\Map\ImmutableMap<string, int>', ImmutableHashMap::ofPairs([['a', 1], ['b', 2]]));
assertType('Noctud\Collection\Map\MutableMap<string, int>', MutableHashMap::of(['a' => 1, 'b' => 2]));
assertType('Noctud\Collection\Map\MutableMap<string, int>', MutableHashMap::ofPairs([['a', 1], ['b', 2]]));
