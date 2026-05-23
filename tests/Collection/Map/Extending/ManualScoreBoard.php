<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection\Map\Extending;

use Closure;
use Noctud\Collection\Map\HashMap\HashKeyValueStore;
use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\Map\ImmutableMapLogic;

/**
 * Extension style #2 (Map): base ImmutableMapLogic + class-level `@method self`
 * overrides + a newMapOf override.
 *
 * @implements ImmutableMap<string, int>
 * @phpstan-consistent-constructor
 * @method self filterValues(Closure(int): bool $predicate)
 * @method self sortedByValueDesc()
 */
class ManualScoreBoard implements ImmutableMap
{
	/** @use ImmutableMapLogic<string, int> */
	use ImmutableMapLogic;

	/** @param iterable<string, int> $data */
	public function __construct(iterable $data = [])
	{
		$this->store = HashKeyValueStore::fromAssoc($data);
	}

	/** @param iterable<string, int> $data */
	protected function newMapOf(iterable $data): ImmutableMap
	{
		return new self($data);
	}

	public function winners(): self
	{
		return $this->filterValues(static fn (int $score): bool => $score >= 100);
	}

	public function ranked(): self
	{
		return $this->sortedByValueDesc();
	}
}
