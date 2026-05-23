<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Collection\Map\Extending;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Verifies that extending a Map self-preserves the subtype at runtime.
 *
 * The fixtures' `: self` domain methods are the compile-time narrowing assertions,
 * since tests/ is analysed by PHPStan at level 9.
 */
final class MapExtendingTest extends TestCase
{
	#[Test]
	public function trait_filter_returns_subtype(): void
	{
		$board = new ScoreBoard(['alice' => 120, 'bob' => 80, 'carol' => 100]);

		$winners = $board->winners();

		self::assertInstanceOf(ScoreBoard::class, $winners);
		self::assertCount(2, $winners);
		self::assertSame(120, $winners['alice']);
	}

	#[Test]
	public function trait_ordering_returns_subtype(): void
	{
		$board = new ScoreBoard(['alice' => 120, 'bob' => 80, 'carol' => 100]);

		$ranked = $board->ranked();
		$byName = $board->byName();

		self::assertInstanceOf(ScoreBoard::class, $ranked);
		self::assertInstanceOf(ScoreBoard::class, $byName);
		self::assertSame(['alice', 'carol', 'bob'], $ranked->keys->toArray());
		self::assertSame(['alice', 'bob', 'carol'], $byName->keys->toArray());
	}

	#[Test]
	public function trait_mutation_returns_subtype(): void
	{
		$board = new ScoreBoard(['alice' => 120]);

		$added = $board->with('bob', 90);
		$removed = $added->without('alice');

		self::assertInstanceOf(ScoreBoard::class, $added);
		self::assertInstanceOf(ScoreBoard::class, $removed);
		self::assertCount(2, $added);
		self::assertCount(1, $removed);
		self::assertSame(90, $removed['bob']);
	}

	#[Test]
	public function trait_chained_returns_subtype(): void
	{
		$board = new ScoreBoard(['alice' => 120, 'bob' => 80, 'carol' => 100]);

		$top = $board->top();

		self::assertInstanceOf(ScoreBoard::class, $top);
		self::assertCount(1, $top);
		self::assertSame(['alice'], $top->keys->toArray());
	}

	#[Test]
	public function manual_filter_and_ordering_return_subtype(): void
	{
		$board = new ManualScoreBoard(['alice' => 120, 'bob' => 80, 'carol' => 100]);

		$winners = $board->winners();
		$ranked = $board->ranked();

		self::assertInstanceOf(ManualScoreBoard::class, $winners);
		self::assertInstanceOf(ManualScoreBoard::class, $ranked);
		self::assertCount(2, $winners);
	}
}
