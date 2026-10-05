<?php

/**
 * This file is part of the Noctud Collection.
 * Copyright (c) Noctud.dev
 */

/**
 * Combines the comparisons bin/compare-benchmarks.sh made on several machines.
 *
 * Each directory holds the compare.tsv and confirm.tsv of one machine. A variant measured again
 * on a machine counts with its second measure, the first one otherwise. A variant is reported when
 * the median of its changes over the machines exceeds the threshold: a slow machine or an unlucky
 * measure no longer decides alone.
 *
 * Usage: php aggregate-benchmarks.php <threshold> <directory>...
 */

declare(strict_types=1);

/**
 * @return array<string, float> The change of the pull request against the base, in %, by variant
 */
function changes(string $file, bool $againstPullRequest): array
{
	$changes = [];
	foreach (is_file($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [] as $line) {
		$columns = str_getcsv($line, "\t", '"', '\\');
		$mode = explode(' ', (string) $columns[5]);
		if (!isset($mode[1])) {
			continue; // No baseline: the subject is new.
		}

		$change = (float) $mode[1];
		// The second measure compares the base against the pull request: turn it around.
		$changes["$columns[0] $columns[1] $columns[2]"] = $againstPullRequest ? 100 / (1 + $change / 100) - 100 : $change;
	}

	return $changes;
}

$threshold = (float) $argv[1];
$machines = array_slice($argv, 2);

$byVariant = [];
foreach ($machines as $directory) {
	$changes = changes("$directory/confirm.tsv", true) + changes("$directory/compare.tsv", false);
	foreach ($changes as $variant => $change) {
		$byVariant[$variant][] = $change;
	}
}

$moved = [];
foreach ($byVariant as $variant => $changes) {
	sort($changes);
	$middle = intdiv(count($changes), 2);
	$median = count($changes) % 2 === 1 ? $changes[$middle] : ($changes[$middle - 1] + $changes[$middle]) / 2;
	if (abs($median) > $threshold) {
		$moved[$variant] = [$median, $changes];
	}
}

$count = count($machines);
if ($moved === []) {
	echo "No variant moved by more than $threshold% over $count machines.\n";
	exit(0);
}

uasort($moved, static fn (array $a, array $b): int => $a[0] <=> $b[0]);
echo "Variants that moved by more than $threshold% over $count machines (median, then the change on each machine, sorted):\n";
foreach ($moved as $variant => [$median, $changes]) {
	printf(
		"  %-60s %+8.1f%%   %s\n",
		$variant,
		$median,
		implode(' ', array_map(static fn (float $change): string => sprintf('%+.0f', $change), $changes)),
	);
}
