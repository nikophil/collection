#!/usr/bin/env bash

# This file is part of the Noctud Collection.
# Copyright (c) Noctud.dev

# Compares the benchmarks of the current checkout with those of another one, class by class.
#
# Each class runs on the other checkout, then right away on this one, so that a slowdown of a
# shared machine hits both sides alike. The variants that moved by more than THRESHOLD% are
# then measured once more the same way: a real change shows again, a slowdown of the machine
# rarely does twice.
#
# Usage: bin/compare-benchmarks.sh <directory of the base checkout>

set -euo pipefail
# awk reads the percentages with a decimal point.
export LC_ALL=C

base=$1
threshold=${THRESHOLD:-10}
root=$PWD
mkdir -p var

# Runs the given benchmark file on both sides, prints the comparison and appends it to a TSV file.
# The local executor runs every iteration in one process: spawning one per iteration would more
# than double the run. --revs is left alone, since it would override the revolutions of the fast
# subjects. --ansi keeps the colours, which tell faster from slower at a glance. The plain progress
# writes one line per variant: the default one rewrites its line with carriage returns, which a
# CI log does not render.
compare() {
	local bench=$1 tsv=$2
	shift 2
	local baseline=()
	if [ -f "$base/$bench" ]; then
		(cd "$base" && vendor/bin/phpbench run "$bench" --executor=local --iterations=4 --progress=none \
			--dump-file="$root/var/base.xml" "$@")
		baseline=(--file=var/base.xml)
	fi
	# The delimited renderer also echoes its file: its lines are the only ones with tabs.
	vendor/bin/phpbench run "$bench" "${baseline[@]}" --executor=local --iterations=4 --stop-on-error \
		--ansi --progress=plain --report=compare --output=console \
		--output='{"renderer": "delimited", "delimiter": "\t", "file": "var/last.tsv"}' "$@" \
		| awk '!/\t/ && !/^Dumped delimited file:$/ && $0 != "var/last.tsv"'
	tail -n +2 var/last.tsv >> "$tsv"
}

# Prints "benchmark <tab> subject <tab> set" for each variant that moved by more than the threshold.
moved() {
	awk -F'\t' -v threshold="$threshold" '{
		gsub(/"/, "", $6)
		split($6, mode, " ")
		diff = mode[2] + 0
		if (mode[2] != "" && (diff > threshold || diff < -threshold)) print $1 "\t" $2 "\t" $3
	}' "$1" | sort -u
}

: > var/compare.tsv
for bench in $(find benchmarks -name '*Bench.php' | sort); do
	compare "$bench" var/compare.tsv
done

if [ -z "$(moved var/compare.tsv)" ]; then
	echo "No variant moved by more than $threshold%."
	exit 0
fi

# Measures the given variants of a benchmark file once more, the pull request first this time, so
# that going second does not favour the same side twice. The remote executor runs every iteration
# in a fresh process: a subject no longer inherits the state the subjects before it left behind.
# The comparison is made from the base side, so the TSV holds the base against the pull request.
remeasure() {
	local bench=$1 filter=$2 variant=$3
	[ -f "$base/$bench" ] || return 0
	# phpbench tells on stderr where it dumped the results.
	vendor/bin/phpbench run "$bench" --filter="$filter" --variant="$variant" --executor=remote --iterations=3 --progress=none \
		--dump-file="$root/var/pr.xml" 2>&1 > /dev/null | awk '!/^Dumped result to /'
	(cd "$base" && vendor/bin/phpbench run "$bench" --filter="$filter" --variant="$variant" --executor=remote --iterations=3 \
		--progress=none --file="$root/var/pr.xml" \
		--report='{"generator": "expression", "cols": ["benchmark", "subject", "set", "revs", "its", "mode", "rstdev"]}' \
		--output="{\"renderer\": \"delimited\", \"delimiter\": \"\\t\", \"file\": \"$root/var/last.tsv\"}" > /dev/null)
	tail -n +2 var/last.tsv >> var/confirm.tsv
}

echo
echo "Measuring once more the variants that moved by more than $threshold%"
: > var/confirm.tsv
for benchmark in $(moved var/compare.tsv | cut -f1 | sort -u); do
	subjects=$(moved var/compare.tsv | awk -F'\t' -v b="$benchmark" '$1 == b { print $2 }' | sort -u | paste -sd'|')
	sets=$(moved var/compare.tsv | awk -F'\t' -v b="$benchmark" '$1 == b { print $3 }' | sort -u | paste -sd'|')
	echo "  $benchmark: ${subjects//|/, }"
	remeasure "$(find benchmarks -name "$benchmark.php")" "::($subjects)\$" "^($sets)\$"
done

echo
echo "Variants that moved by more than $threshold%, measured twice:"
awk -F'\t' -v threshold="$threshold" '
	{ gsub(/"/, "", $6); split($6, mode, " "); key = $1 " " $2 " " $3 }
	FNR == NR { first[key] = mode[2] + 0; next }
	(key in first) && (first[key] > threshold || first[key] < -threshold) {
		# The second measure compares the base against the pull request: turn it around.
		second = 100 / (1 + mode[2] / 100) - 100
		real = (second > threshold && first[key] > 0) || (second < -threshold && first[key] < 0)
		printf "  %-60s %+8.1f%% %+8.1f%%  %s\n", key, first[key], second, real ? "confirmed" : "noise"
	}
' var/compare.tsv var/confirm.tsv
