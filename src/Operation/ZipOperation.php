<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Operation;

use Generator;
use Traversable;

/**
 * @internal
 * @template V
 * @extends AbstractOperation<int,V>
 */
final class ZipOperation extends AbstractOperation
{
	/**
	 * @template U
	 * @param iterable<U> $other
	 * @return Generator<array{V, U}>
	 */
	public function with(iterable $other): Generator
	{
		$otherArray = $other instanceof Traversable ? iterator_to_array($other, false) : array_values($other);
		$otherCount = count($otherArray);
		$i = 0;

		foreach ($this->data as $v) {
			if ($i >= $otherCount) {
				break;
			}

			yield [$v, $otherArray[$i]];
			$i++;
		}
	}
}
