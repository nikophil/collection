# Benchmarks

Performance benchmarks, run with [PHPBench](https://phpbench.readthedocs.io). Every subject runs in its own PHP
process with Xdebug off and OPcache on (see `phpbench.json`), against deterministic data so that two runs compare
the same work.

## Running

```shell
composer bench                                 # everything, ~1,800 variants in ~13 minutes
composer bench -- --group=list                 # one group
composer bench -- --filter='SetBench::benchAdd' # one subject
```

Groups: `list`, `set`, `map`, `mutation`, `hashing`, `sequence`, `factory`, `pipeline`, `native`.

## Comparing against a baseline

```shell
git switch main && composer bench:baseline     # tags the run as "baseline" in var/phpbench
git switch my-branch && composer bench:compare # shows the difference of every subject
```

Both use more iterations and retry until the deviation is under 5%, so they are slower than `composer bench`:
narrow them with `--group` or `--filter` when possible. An extra `--assert="mode(variant.time.avg) <= mode(baseline.time.avg) +/- 10%"`
turns a regression into a failure.

## Layout

| Directory     | Contents                                                                                          |
|---------------|---------------------------------------------------------------------------------------------------|
| `Collection/` | Traits covering the Collection API, shared by the List and Set benchmarks                         |
| `List/`       | `ListBench` (reads), `ImmutableListBench` (copying writes), `MutableListBench` (in-place writes)  |
| `Set/`        | The same for Set, plus `SetElementTypeBench` comparing the hashing of each element type           |
| `Map/`        | One class per implementation (HashMap, StringMap, IntMap), plus `MapKeyTypeBench` for key types   |
| `Sequence/`   | Intermediate and terminal operations                                                              |
| `Native/`     | Plain arrays doing the same work, under the subject names of their collection counterparts        |
| `Fixture/`    | Data generators and parameter providers                                                           |

`FactoryBench` covers the factory functions and `PipelineBench` compares eager, lazy and native chains.

## Writing a benchmark

- Classes end with `Bench`, subjects start with `bench`. Traits must not end with `Bench.php`, or PHPBench would
  load them as benchmarks.
- Subjects return their result, so that `#[NoDiscard]` methods are not flagged and the result is not optimized away.
- Attributes on a trait are ignored by PHPBench: put them on the class or on the methods.
- Operations that are too cheap to measure (a lookup, an insertion) run over a batch of calls spread across the
  collection. Subjects that take a couple of microseconds or less on every size use
  `#[Revs(self::FastSubjectRevs)]`: with the default revolutions, an iteration is too short to be measured reliably.
- In-place mutations start from a copy of the fixture, taken inside the subject: the copy shares the source array
  until its first write, so every revolution works on the same state.

