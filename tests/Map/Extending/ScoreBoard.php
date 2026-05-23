<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Map\Extending;

use Noctud\Collection\Map\HashMap\HashKeyValueStore;
use Noctud\Collection\Map\ImmutableMap;
use Noctud\Collection\Map\SelfPreservingImmutableMapLogic;

/**
 * Extension style #1 (Map): the SelfPreservingImmutableMapLogic trait.
 *
 * @implements ImmutableMap<string, int>
 * @phpstan-consistent-constructor
 */
class ScoreBoard implements ImmutableMap
{
	/** @use SelfPreservingImmutableMapLogic<string, int> */
	use SelfPreservingImmutableMapLogic;

	/** @param iterable<string, int> $data */
	public function __construct(iterable $data = [])
	{
		$this->store = HashKeyValueStore::fromAssoc($data);
	}

	public function winners(): self
	{
		return $this->filterValues(static fn (int $score): bool => $score >= 100);
	}

	public function ranked(): self
	{
		return $this->sortedByValueDesc();
	}

	public function byName(): self
	{
		return $this->sortedByKey();
	}

	public function with(string $name, int $score): self
	{
		return $this->put($name, $score);
	}

	public function without(string $name): self
	{
		return $this->remove($name);
	}

	public function top(): self
	{
		return $this->sortedByValueDesc()->takeFirst(1);
	}
}
