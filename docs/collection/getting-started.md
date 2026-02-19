# Getting Started

## Installation

```bash
composer require noctud/collection:0.1.0-beta1
```

Requires **PHP 8.4** or later.

## Architecture

Three core types, each in mutable and immutable variants:

:::tabs
== Simplified
```
Collection<E>            → Ordered elements, read-only
├── List<E>              → Indexed, array access
│   ├── ImmutableList<E> → Mutation returns new
│   └── MutableList<E>   → Mutation returns itself
└── Set<E>               → Unique values, no array access
    ├── ImmutableSet<E>  → Mutation returns new
    └── MutableSet<E>    → Mutation returns itself

Map<K,V>                 → Ordered key-value pairs, array access
├── ImmutableMap<K,V>    → Mutation returns new
└── MutableMap<K,V>      → Mutation returns itself
```
== Full hierarchy
```
Collection<E>                → Ordered elements, read-only
├── ImmutableCollection<E>   → Mutation returns new (#[NoDiscard])
├── WritableCollection<E>    → Writable methods (add/remove)
│   └── MutableCollection<E> → Positional control (addFirst/sort)
├── Set<E>                   → Unique values, no array access
│   ├── ImmutableSet<E>
│   └── WritableSet<E>
│       └── MutableSet<E>
└── List<E>                  → Indexed, array access
    ├── ImmutableList<E>
    └── WritableList<E>
        └── MutableList<E>

Map<K,V>                     → Ordered key-value pairs, array access
├── ImmutableMap<K,V>
└── WritableMap<K,V>
    └── MutableMap<K,V>
```
:::

::: tip Extensible by design
Every node in this hierarchy is an **interface**, not a class. The library ships with default implementations backed by logic traits, but any library or application can provide its own implementation. See the [Extending](./extending) guide for details.
:::

The API is heavily inspired by [Kotlin Collections](https://kotlinlang.org/docs/collections-overview.html) — factory functions, the mutable/immutable split, `OrNull` conventions, and method naming all follow Kotlin patterns. 

The type hierarchy draws from the Java Collections Framework with clear separation of mutability/immutability.

## Creating Collections

Use the factory functions from the `Noctud\Collection` namespace:

```php
use function Noctud\Collection\listOf; // mutableListOf, setOf, ...
```
Universal Immutable and Mutable collections:
```php
$list = listOf([1, 2, 3]); // ImmutableList<int>
$set = setOf(['a', 'b', 'c']); // ImmutableSet<string>
$map = mapOf(['a' => 'b']); // ImmutableMap<string, string>
```
```php
$list = mutableListOf([1, 2, 3]); // MutableList<int>
$set = mutableSetOf(['a', 'b', 'c']); // MutableSet<string>
$map = mutableMapOf(['a' => 'b']); // MutableMap<string, string>
```

Optimized **string-keyed only** Maps (int keys are only allowed when constructing from array)

```php
$immutable = stringMapOf(['1' => 'a']); // ImmutableMap<string, string>
$mutable = mutableStringMapOf(['1' => 'a']); // MutableMap<string, string>
```

Optimized **int-keyed only** Maps (keys must be int, even when constructing from array)

```php
$immutable = intMapOf([1 => 'a']); // ImmutableMap<int, string>
$mutable = mutableIntMapOf([1 => 'a']); // MutableMap<int, string>
```

::: tip Object keys? Use `mapOfPairs`
PHP arrays only support `string|int` keys, so `mapOf()` can't accept objects as keys. Use `mapOfPairs()` (or `mutableMapOfPairs()`) with `[key, value]` tuples instead:
```php
$user = new User(1);
$map = mapOfPairs([[$user, 'admin']]); // ImmutableMap<User, string>
$map = mutableMapOfPairs([[$user, 'admin']]); // MutableMap<User, string>
```
:::

## Accessing Elements

Array access is strict — it throws on missing keys/indices, just like `get()`. Use the `??` operator for safe fallback, or `getOrNull()` for the nullable variant:

```php
$list[0]; // throws if index is out of bounds
$list[0] ?? null; // null if index is out of bounds
$list->getOrNull(0); // null if index is out of bounds

$map['key']; // throws if key is missing
$map['key'] ?? null; // null if key is missing
$map->getOrNull('key'); // null if key is missing
```

Sets do not support indexed access. Use `contains()` instead:

```php
$set->contains('a'); // true
```

## Transforming

Transformation methods (`filter`, `map`, `sorted`, etc.) always return **immutable collections**, regardless of whether the source is mutable or immutable. Chain them freely:

```php
// [50, 40, 30]
$result = mutableListOf([1, 2, 3, 4, 5])
    ->filter(fn($n) => $n > 2) // ImmutableList
    ->map(fn($n) => $n * 10) // ImmutableList
    ->reversed(); // ImmutableList
```

## Iterating

All collections are `Traversable`, there are 2 ways to iterate:

```php
foreach ($map as $key => $value) {
    echo "$key = $value\n";
}

// returns itself for chaining
$set->forEach(fn($el) => process($el));
```

## What's Next

- [List guide](./list) — indexed sequences
- [Set guide](./set) — unique element collections
- [Map guide](./map) — key-value pairs with type-safe keys
- [Mutability](./mutability) — mutable vs immutable patterns
- [Lazy Initialization](./lazy-init) — deferred initialization
- [Sorting](./sorting) — comprehensive sorting reference
- [API Reference](./api/collection) — complete method listing
