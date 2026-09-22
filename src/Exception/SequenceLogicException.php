<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Exception;

/**
 * Marks the failures any Sequence method can raise, whatever that method does: a source
 * that refuses to hand back a fresh pass, or one that hands back something not iterable.
 *
 * Every terminal operation declares this single type rather than the concrete exceptions
 * behind it - which one surfaces depends on the source, not on the method called.
 */
interface SequenceLogicException extends NoctudCollectionException
{
}
