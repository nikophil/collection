<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Exception;

use Throwable;

/**
 * Marks every exception this library raises, so a single catch covers them all.
 *
 * An interface rather than a base class: each failure keeps the parent that describes it best
 * - LogicException for all of them today - and a third-party collection built on this library
 * can join the family without giving up the exception hierarchy it already has.
 */
interface NoctudCollectionException extends Throwable
{
}
