<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection;

use PHPUnit\Framework\Attributes\Test;

trait CollectionForEach
{
	#[Test]
	public function forEach_visits_all_elements(): void
	{
		$collection = $this->collectionOf([1, 2, 3]);
		$visited = [];
		$collection->forEach(function ($v) use (&$visited) {
			$visited[] = $v;
		});
		$this->assertSame([1, 2, 3], $visited);
	}

	#[Test]
	public function forEach_on_empty(): void
	{
		$collection = $this->collectionOf([]);
		$visited = [];
		$collection->forEach(function ($v) use (&$visited) {
			$visited[] = $v;
		});
		$this->assertSame([], $visited);
	}
}
