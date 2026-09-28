<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Benchmarks\Fixture;

use Generator;

/**
 * @phpstan-type ElementType 'int'|'string'|'float'|'object'|'hashable'|'array'
 */
trait ProvidesElementTypes
{
	/**
	 * Each type goes through a different branch of KeyHasher.
	 *
	 * @return Generator<string, array{type: ElementType}>
	 */
	public function provideElementTypes(): Generator
	{
		yield 'int' => ['type' => 'int'];
		yield 'string' => ['type' => 'string'];
		yield 'float' => ['type' => 'float'];
		yield 'object' => ['type' => 'object'];
		yield 'hashable' => ['type' => 'hashable'];
		yield 'array' => ['type' => 'array'];
	}

	/**
	 * @param ElementType $type
	 * @return list<mixed>
	 */
	private static function elementsOfType(string $type, int $size, int $offset = 0): array
	{
		return match ($type) {
			'int' => Data::ints($size, $offset),
			'string' => Data::strings($size, $offset),
			'float' => Data::floats($size, $offset),
			'object' => Data::objects($size, $offset),
			'hashable' => Data::hashables($size, $offset),
			'array' => Data::arrays($size, $offset),
		};
	}
}
