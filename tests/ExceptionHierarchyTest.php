<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests;

use Generator;
use Noctud\Collection\Exception\NoctudCollectionException;
use Noctud\Collection\Exception\SourceException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function Noctud\Collection\listOf;
use function Noctud\Collection\sequenceOf;

final class ExceptionHierarchyTest extends TestCase
{
	#[Test]
	public function a_source_that_cannot_replay_is_caught_as_a_sequence_failure(): void
	{
		$generator = (static function (): Generator {
			yield 1;
		})();
		$sequence = sequenceOf($generator);

		$this->assertSame([1], $sequence->toArray());

		$this->expectException(SourceException::class);

		$_ = $sequence->toArray(); // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
	}

	#[Test]
	public function a_source_returning_a_non_iterable_is_caught_as_a_sequence_failure(): void
	{
		/** @phpstan-ignore argument.type, argument.templateType */
		$sequence = sequenceOf(static fn (): int => 42);

		$this->expectException(SourceException::class);

		$_ = $sequence->toArray(); // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
	}

	#[Test]
	public function a_sequence_failure_is_also_caught_at_the_library_root(): void
	{
		/** @phpstan-ignore argument.type, argument.templateType */
		$sequence = sequenceOf(static fn (): int => 42);

		$this->expectException(NoctudCollectionException::class);

		$_ = $sequence->toArray(); // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
	}

	#[Test]
	public function an_eager_failure_is_caught_at_the_library_root(): void
	{
		$this->expectException(NoctudCollectionException::class);

		$_ = listOf([])->first(); // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
	}
}
