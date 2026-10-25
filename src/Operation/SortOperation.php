<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Operation;

/**
 * @internal
 */
final class SortOperation
{
	/**
	 * Orders the values a selector returned, keeping their keys: array_replace($order, $data)
	 * then puts the data selected from in that order.
	 *
	 * The selector runs once per element, before sorting: a usort() comparator calling it would
	 * run it twice per comparison. asort() compares like <=> and keeps equal values in their
	 * order, as usort() does.
	 *
	 * @template K of array-key
	 * @param array<K,mixed> $selected
	 * @return array<K,mixed>
	 */
	public static function order(array $selected, bool $descending = false): array
	{
		if ($descending) {
			arsort($selected);
		} else {
			asort($selected);
		}

		return $selected;
	}
}
