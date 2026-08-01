<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

declare(strict_types=1);

namespace Noctud\Collection\Tests\Sequence;

use ArrayIterator;
use Generator;
use LimitIterator;
use Noctud\Collection\Tests\Collection\Fixture\Cat;
use Noctud\Collection\Tests\Collection\Fixture\Dog;
use Noctud\Collection\Tests\Collection\Fixture\Walkable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SplStack;
use function Noctud\Collection\listOf;
use function Noctud\Collection\sequenceOf;

final class SequenceTransformTest extends TestCase
{
	#[Test]
	public function filter_keeps_matching_elements_reindexed(): void
	{
		$this->assertSame([2, 4], sequenceOf([1, 2, 3, 4])->filter(static fn (int $v): bool => $v % 2 === 0)->toArray());
	}

	#[Test]
	public function filter_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3, 4, 5];
		$predicate = static fn (int $v): bool => $v % 2 === 1;

		$this->assertSame(
			listOf($data)->filter($predicate)->toArray(),
			sequenceOf($data)->filter($predicate)->toArray(),
		);
	}

	#[Test]
	public function filterNotNull_drops_null_elements(): void
	{
		$this->assertSame([1, 2], sequenceOf([1, null, 2, null])->filterNotNull()->toArray());
	}

	#[Test]
	public function filterNotNull_matches_its_collection_counterpart(): void
	{
		$data = [null, 1, null, 2];

		$this->assertSame(
			listOf($data)->filterNotNull()->toArray(),
			sequenceOf($data)->filterNotNull()->toArray(),
		);
	}

	#[Test]
	public function filterInstanceOf_keeps_only_instances_of_the_given_type(): void
	{
		$dog = new Dog('Rex');
		$cat = new Cat('Felix');

		$this->assertSame([$dog], sequenceOf([$dog, $cat])->filterInstanceOf(Walkable::class)->toArray());
	}

	#[Test]
	public function filterInstanceOf_matches_its_collection_counterpart(): void
	{
		$data = [new Dog('Rex'), new Cat('Felix'), new Dog('Bobby')];

		$this->assertSame(
			listOf($data)->filterInstanceOf(Walkable::class)->toArray(),
			sequenceOf($data)->filterInstanceOf(Walkable::class)->toArray(),
		);
	}

	#[Test]
	public function map_transforms_each_element(): void
	{
		$this->assertSame(
			['1', '2'],
			sequenceOf([1, 2])
				->map(static fn (int $v): string => (string) $v)
				->toArray()
		);
	}

	#[Test]
	public function map_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3];
		$transform = static fn (int $v): int => $v * $v;

		$this->assertSame(
			listOf($data)->map($transform)->toArray(),
			sequenceOf($data)->map($transform)->toArray(),
		);
	}

	#[Test]
	public function mapNotNull_drops_the_elements_the_transform_maps_to_null(): void
	{
		$this->assertSame(
			[20, 40],
			sequenceOf([1, 2, 3, 4])
				->mapNotNull(static fn (int $v): ?int => $v % 2 === 0 ? $v * 10 : null)
				->toArray(),
		);
	}

	#[Test]
	public function mapNotNull_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3, 4, 5];
		$transform = static fn (int $v): ?string => $v > 2 ? "#$v" : null;

		$this->assertSame(
			listOf($data)->mapNotNull($transform)->toArray(),
			sequenceOf($data)->mapNotNull($transform)->toArray(),
		);
	}

	#[Test]
	public function flatMap_concatenates_the_iterables_returned_by_the_transform(): void
	{
		$this->assertSame(
			[1, 10, 2, 20],
			sequenceOf([1, 2])->flatMap(static fn (int $v): array => [$v, $v * 10])->toArray(),
		);
	}

	#[Test]
	public function flatMap_matches_its_collection_counterpart(): void
	{
		$data = ['ab', 'c'];
		$transform = static fn (string $v): array => str_split($v);

		$this->assertSame(
			listOf($data)->flatMap($transform)->toArray(),
			sequenceOf($data)->flatMap($transform)->toArray(),
		);
	}

	#[Test]
	public function flatMap_accepts_a_generator_returning_transform(): void
	{
		$result = sequenceOf([1, 2])
			->flatMap(static function (int $v): Generator {
				yield $v;
				yield -$v;
			})
			->toArray();

		$this->assertSame([1, -1, 2, -2], $result);
	}

	#[Test]
	public function flatten_unwraps_one_level_and_keeps_non_iterable_elements(): void
	{
		$this->assertSame([1, 2, 3, 4], sequenceOf([[1, 2], [3], 4])->flatten()->toArray());
	}

	#[Test]
	public function flatten_matches_its_collection_counterpart(): void
	{
		$data = [[1, 2], [], [3], 4];

		$this->assertSame(
			listOf($data)->flatten()->toArray(),
			sequenceOf($data)->flatten()->toArray(),
		);
	}

	#[Test]
	public function takeFirst_keeps_the_first_n_elements(): void
	{
		$this->assertSame([1, 2], sequenceOf([1, 2, 3, 4])->takeFirst(2)->toArray());
	}

	#[Test]
	public function takeFirst_defaults_to_one_element_and_accepts_zero(): void
	{
		$this->assertSame([1], sequenceOf([1, 2, 3])->takeFirst()->toArray());
		$this->assertSame([], sequenceOf([1, 2, 3])->takeFirst(0)->toArray());
	}

	#[Test]
	public function takeFirst_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3];

		foreach ([0, 1, 2, 3, 10] as $n) {
			$this->assertSame(
				listOf($data)->takeFirst($n)->toArray(),
				sequenceOf($data)->takeFirst($n)->toArray(),
			);
		}
	}

	#[Test]
	public function dropFirst_skips_the_first_n_elements(): void
	{
		$this->assertSame([3, 4], sequenceOf([1, 2, 3, 4])->dropFirst(2)->toArray());
	}

	#[Test]
	public function dropFirst_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3];

		foreach ([0, 1, 2, 3, 10] as $n) {
			$this->assertSame(
				listOf($data)->dropFirst($n)->toArray(),
				sequenceOf($data)->dropFirst($n)->toArray(),
			);
		}
	}

	#[Test]
	public function takeWhile_stops_at_the_first_element_failing_the_predicate(): void
	{
		$this->assertSame([1, 2], sequenceOf([1, 2, 3, 1])->takeWhile(static fn (int $v): bool => $v < 3)->toArray());
	}

	#[Test]
	public function takeWhile_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3, 1];
		$predicate = static fn (int $v): bool => $v < 3;

		$this->assertSame(
			listOf($data)->takeWhile($predicate)->toArray(),
			sequenceOf($data)->takeWhile($predicate)->toArray(),
		);
	}

	#[Test]
	public function dropWhile_yields_everything_from_the_first_element_failing_the_predicate(): void
	{
		$this->assertSame([3, 1], sequenceOf([1, 2, 3, 1])->dropWhile(static fn (int $v): bool => $v < 3)->toArray());
	}

	#[Test]
	public function dropWhile_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3, 1];
		$predicate = static fn (int $v): bool => $v < 3;

		$this->assertSame(
			listOf($data)->dropWhile($predicate)->toArray(),
			sequenceOf($data)->dropWhile($predicate)->toArray(),
		);
	}

	#[Test]
	public function distinct_keeps_the_first_occurrence_of_each_element(): void
	{
		$this->assertSame([1, 2, 3], sequenceOf([1, 2, 1, 3, 2])->distinct()->toArray());
	}

	#[Test]
	public function distinct_matches_its_collection_counterpart(): void
	{
		$data = ['a', 'b', 'a', 'c'];

		$this->assertSame(
			listOf($data)->distinct()->toArray(),
			sequenceOf($data)->distinct()->toArray(),
		);
	}

	#[Test]
	public function distinctBy_keeps_the_first_element_of_each_selector_value(): void
	{
		$this->assertSame(
			['one', 'three'],
			sequenceOf(['one', 'two', 'three'])->distinctBy(static fn (string $v): int => strlen($v))->toArray(),
		);
	}

	#[Test]
	public function distinctBy_matches_its_collection_counterpart(): void
	{
		$data = ['one', 'two', 'three', 'six'];
		$selector = static fn (string $v): int => strlen($v);

		$this->assertSame(
			listOf($data)->distinctBy($selector)->toArray(),
			sequenceOf($data)->distinctBy($selector)->toArray(),
		);
	}

	#[Test]
	public function zip_pairs_elements_at_the_same_position(): void
	{
		$this->assertSame(
			[[1, 'a'], [2, 'b']],
			sequenceOf([1, 2])->zip(['a', 'b'])->toArray(),
		);
	}

	#[Test]
	public function zip_stops_at_the_shorter_side(): void
	{
		$this->assertSame([[1, 'a']], sequenceOf([1, 2, 3])->zip(['a'])->toArray());
		$this->assertSame([[1, 'a']], sequenceOf([1])->zip(['a', 'b', 'c'])->toArray());
		$this->assertSame([], sequenceOf([1, 2])->zip([])->toArray());
	}

	#[Test]
	public function zip_accepts_any_iterable_on_the_other_side(): void
	{
		$other = (static function (): Generator {
			yield 'a';
			yield 'b';
		})();

		$this->assertSame([[1, 'a'], [2, 'b']], sequenceOf([1, 2, 3])->zip($other)->toArray());
		$this->assertSame([[1, 'a']], sequenceOf([1, 2])->zip(sequenceOf(['a']))->toArray());
	}

	#[Test]
	public function zip_positions_an_unrewound_iterator_on_the_other_side(): void
	{
		$stack = new SplStack();
		$stack->push('a');
		$stack->push('b');

		// valid() answers false on a stack that has never been rewound, however many elements
		// it holds: without positioning it, lockstep would read an empty side and pair nothing.
		$this->assertCount(2, $stack);

		$this->assertSame([[1, 'b'], [2, 'a']], sequenceOf([1, 2])->zip($stack)->toArray());
	}

	#[Test]
	public function zip_positions_an_iterator_decorator_on_the_other_side(): void
	{
		$other = new LimitIterator(new ArrayIterator(['a', 'b', 'c']), 0, 2);

		$this->assertSame([[1, 'a'], [2, 'b']], sequenceOf([1, 2])->zip($other)->toArray());
	}

	#[Test]
	public function zip_matches_its_collection_counterpart(): void
	{
		$data = [1, 2, 3];
		$other = ['a', 'b'];

		$this->assertSame(
			listOf($data)->zip($other)->toArray(),
			sequenceOf($data)->zip($other)->toArray(),
		);
	}

	#[Test]
	public function zipWithNext_pairs_adjacent_elements(): void
	{
		$this->assertSame([[1, 2], [2, 3]], sequenceOf([1, 2, 3])->zipWithNext()->toArray());
	}

	#[Test]
	public function zipWithNext_yields_nothing_below_two_elements(): void
	{
		$this->assertSame([], sequenceOf([1])->zipWithNext()->toArray());
		$this->assertSame([], sequenceOf([])->zipWithNext()->toArray());
	}

	#[Test]
	public function zipWithNext_matches_its_collection_counterpart(): void
	{
		$data = ['a', 'b', 'c'];

		$this->assertSame(
			listOf($data)->zipWithNext()->toArray(),
			sequenceOf($data)->zipWithNext()->toArray(),
		);
	}

	#[Test]
	public function onEach_yields_the_elements_unchanged(): void
	{
		$seen = [];
		$action = static function (int $v) use (&$seen): void {
			$seen[] = $v;
		};

		$this->assertSame([1, 2, 3], sequenceOf([1, 2, 3])->onEach($action)->toArray());
		$this->assertSame([1, 2, 3], $seen);
	}

	#[Test]
	public function predicate_receives_positional_index(): void
	{
		$this->assertSame(
			['a', 'c'],
			sequenceOf(['a', 'b', 'c', 'd'])->filter(static fn (string $v, int $i): bool => $i % 2 === 0)->toArray(),
		);
	}

	#[Test]
	public function map_after_filter_receives_reindexed_positions(): void
	{
		$result = sequenceOf(['a', 'b', 'c', 'd'])
			->filter(static fn (string $v): bool => $v !== 'b')
			->map(static fn (string $v, int $i): string => "$i:$v")
			->toArray();

		$this->assertSame(['0:a', '1:c', '2:d'], $result);
	}

	#[Test]
	public function flatMap_after_filter_receives_reindexed_positions(): void
	{
		$result = sequenceOf(['a', 'b', 'c'])
			->filter(static fn (string $v): bool => $v !== 'a')
			->flatMap(static fn (string $v, int $i): array => ["$i:$v"])
			->toArray();

		$this->assertSame(['0:b', '1:c'], $result);
	}

	#[Test]
	public function mapNotNull_after_filter_receives_reindexed_positions(): void
	{
		$result = sequenceOf(['a', 'b', 'c'])
			->filter(static fn (string $v): bool => $v !== 'a')
			->mapNotNull(static fn (string $v, int $i): string => "$i:$v")
			->toArray();

		$this->assertSame(['0:b', '1:c'], $result);
	}

	#[Test]
	public function takeWhile_after_filter_receives_reindexed_positions(): void
	{
		$result = sequenceOf(['a', 'b', 'c', 'd'])
			->filter(static fn (string $v): bool => $v !== 'a')
			->takeWhile(static fn (string $v, int $i): bool => $i < 2)
			->toArray();

		$this->assertSame(['b', 'c'], $result);
	}

	#[Test]
	public function dropWhile_after_filter_receives_reindexed_positions(): void
	{
		$result = sequenceOf(['a', 'b', 'c', 'd'])
			->filter(static fn (string $v): bool => $v !== 'a')
			->dropWhile(static fn (string $v, int $i): bool => $i < 2)
			->toArray();

		$this->assertSame(['d'], $result);
	}

	#[Test]
	public function distinctBy_after_filter_receives_reindexed_positions(): void
	{
		$result = sequenceOf(['a', 'b', 'c', 'd', 'e'])
			->filter(static fn (string $v): bool => $v !== 'a')
			->distinctBy(static fn (string $v, int $i): int => intdiv($i, 2))
			->toArray();

		$this->assertSame(['b', 'd'], $result);
	}

	#[Test]
	public function onEach_after_filter_receives_reindexed_positions(): void
	{
		$seen = [];
		$action = static function (string $v, int $i) use (&$seen): void {
			$seen[] = "$i:$v";
		};

		$result = sequenceOf(['a', 'b', 'c'])
			->filter(static fn (string $v): bool => $v !== 'a')
			->onEach($action)
			->toArray();

		$this->assertSame(['b', 'c'], $result);
		$this->assertSame(['0:b', '1:c'], $seen);
	}
}
