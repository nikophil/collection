<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Exception;

use LogicException;

/**
 * Thrown when a mutating method is called on an immutable collection/map or when a
 * requested operation is not valid in the current state (e.g., removing from an empty list).
 */
class UnsupportedOperationException extends LogicException
{
}
