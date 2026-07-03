# KeyValue Helper

`Hoo\WordPressPluginFramework\Helpers\KeyValue\Helper` reads and modifies nested
`array|object` structures through string paths. It is **type-deterministic**
(the path declares whether each level is an object or an array — nothing is
ever guessed) and **immutable** (writes return a new structure; the input is
never touched).

```php
use Hoo\WordPressPluginFramework\Helpers\KeyValue\Helper;

$helper = new Helper(); // or inject HelperInterface via the container
```

## Path syntax

| Accessor | Meaning | Container must be |
|---|---|---|
| `name` / `.name` | object property `name` | object |
| `[key]` | array element `key` | array |
| `*` / `.*` | every property | object |
| `[*]` | every element | array |

- The **first token declares the root type**: `user.name` means the root is an
  object; `[user][name]` means the root is an array.
- Accessors compose freely: `items[0].name` — array `items`, element `0`,
  object property `name`.
- **`a.0` and `a[0]` are different addresses**: the first is object property
  `"0"`, the second is array index `0`. The notation decides, never the data.

### Escaping

Escape reserved characters with a backslash:

| Path | Addresses |
|---|---|
| `a\.b` | property literally named `a.b` |
| `a\[b` | property `a[b` |
| `[a\]b]` | array key `a]b` |
| `[a\\b]` | array key `a\b` |
| `\*` / `[\*]` | property / key literally named `*` (not a wildcard) |

Inside `[...]` dots are literal (`[a.b]` addresses the key `a.b` as-is);
in property segments `]` is literal. UTF-8 keys need no escaping:
`ключ[значение]` works.

A malformed path (`''`, `.a`, `a..b`, `[]`, `[a`, trailing `\`) throws
`InvalidArgumentException` from **every** method — bad syntax is never a soft miss.

## Reading

### `value($data, $path): mixed` — one value

```php
$data = (object) ['user' => (object) ['name' => 'Bob', 'tags' => ['a', 'b']]];

$helper->value($data, 'user.name');     // 'Bob'
$helper->value($data, 'user.tags[1]');  // 'b'
$helper->value($data, 'user.missing');  // null (miss)
$helper->value([10, 20], '[1]');        // 20 (array root)
```

Reads are **total** — they never throw for data reasons. A missing key, a type
mismatch (`name` on an array, `[0]` on an object), or descending into a scalar
is simply a **miss** → `null`.

With a wildcard anywhere in the path, `value()` returns a **list** (possibly
empty — never `null`):

```php
$data = (object) ['rows' => [(object) ['n' => 1], (object) ['n' => 2]]];

$helper->value($data, 'rows[*].n');   // [1, 2]
$helper->value($data, 'nope[*]');     // []
```

Reads return the **actual nested value**, not a copy — mutating a returned
object mutates the source.

### `values($data, $path): array` — resolved-path map, presence-aware

`values()` returns `resolvedPath => value` for every match, and **omits
misses entirely**. This is how you distinguish *found `null`* from *absent*
(`value()` returns `null` for both):

```php
$helper->values((object) ['a' => null], 'a');  // ['a' => null]  — present
$helper->values((object) [], 'a');             // []             — absent

$helper->values($data, 'rows[*].n');
// ['rows[0].n' => 1, 'rows[1].n' => 2]
```

Falsy values (`false`, `0`, `''`, `'0'`, `null`) are values like any other —
presence is decided by key existence, never truthiness. Never test a result
with `if ($value)`; use `values()` when you need a presence check.

Every key in the result is a **canonical, escaped path**: you can feed it back
into `value()` / `withValue()` and it addresses the same location, even when
data keys contain `.`, `]`, `\` or `*`.

## Writing

### `withValue($data, $path, $value): array|object`

Returns a **new** structure; capture the return value:

```php
$data = (object) ['user' => (object) ['name' => 'Bob']];

$data = $helper->withValue($data, 'user.name', 'Alice');
```

**Missing intermediates are created with the type the next accessor demands:**

```php
$helper->withValue(new stdClass(), 'a.b', 9);    // {"a":{"b":9}}
$helper->withValue(new stdClass(), 'a[0]', 9);   // {"a":[9]}
$helper->withValue(new stdClass(), 'a[0].b', 9); // {"a":[{"b":9}]}
$helper->withValue(new stdClass(), 'a.0', 9);    // {"a":{"0":9}}   — property "0"
$helper->withValue(new stdClass(), 'a[0]', 9);   // {"a":[9]}       — index 0
```

**Writes are strict.** If the path's declared type conflicts with real data,
you get a `HelperException` — the helper refuses to coerce or destroy:

```php
$helper->withValue([1, 2], 'name', 9);                    // throws: expects object but found array
$helper->withValue((object) ['a' => 5], 'a.b', 9);        // throws: expects object but found int
$helper->withValue((object) ['a' => null], 'a.b', 9);     // throws: null is not a container
$helper->withValue([], 'a', 9);                           // throws: [] is an array, path says object
```

The last case matters: an **empty array root is still an array**. If you want
an object result, start from `new stdClass()`.

Wildcards write to every child (and create missing leaves on object children):

```php
$helper->withValue($data, 'rows[*].seen', true);  // sets/creates seen on every row
$helper->withValue($grid, 'x[*][*]', 0);          // zeroes a matrix
```

A wildcard over children of the wrong type **throws** (writes are strict per
child); a wildcard over an empty container is a no-op.

Notes:

- Setting a leaf **replaces** it, containers included: `withValue($d, 'a', 9)`
  on `{"a":{"b":1}}` yields `{"a":9}`.
- `null` is a legal value to write: `{"a":null}`.
- Written containers are stored **by identity** (no deep copy of your value).
- Writing a gap index (`x[5]` on a 1-element list) produces a keyed array —
  its JSON shape becomes `{"0":...,"5":...}`, not a list.
- If a write throws, the original structure is guaranteed untouched.

## Removing

### `withoutValue($data, $path): array|object`

```php
$helper->withoutValue((object) ['a' => 1, 'b' => 2], 'a');   // {"b":2}
$helper->withoutValue($data, 'user.tags[0]');                 // removes one element
$helper->withoutValue($data, 'rows[*].debug');                // strips a key from every row
$helper->withoutValue($data, 'rows[*]');                      // empties the list → []
```

Removes are **tolerant** like reads: a missing path or a type mismatch is a
no-op, never an exception.

Semantics worth knowing:

- **Lists reindex**: removing `x[1]` from `[1,2,3]` gives `[1,3]` (stays a
  JSON array). Associative or gapped arrays keep their keys.
- **Emptied containers keep their type**: removing the last property of an
  object leaves `{}`; the last element of a list leaves `[]`.
- Exception: clearing an *associative* array with `[*]` yields `[]` — an empty
  PHP array has no "assoc-ness" to preserve. If an empty container must stay
  `{}` in JSON, model it as `stdClass`, not as an array.
- A property whose value is `null` **is removable** (presence, not truthiness).

## Immutability model

Every write/remove returns a new structure built by **clone-on-write**:
containers *on* the modified path are cloned (objects) or copied (arrays);
containers *off* the path are **shared by reference** with the original.
Cheap, safe, and predictable:

```php
$result = $helper->withValue($data, 'a.b', 9);

$data->a->b;        // unchanged
$result->shared === $data->shared; // true — untouched subtree, same instance
```

Because untouched subtrees are shared, treat both the input and the result as
read-only values — that is the intended usage pattern.

## Errors

| Exception | When | Thrown by |
|---|---|---|
| `InvalidArgumentException` | malformed path syntax | all methods |
| `HelperException` | path type conflicts with data | `withValue()` only |

`value()` / `values()` / `withoutValue()` never throw for data reasons.

## Quick reference

| I want to… | Call |
|---|---|
| read one value | `value($d, 'a.b')` / `value($d, '[k][0]')` |
| read many / with paths | `values($d, 'rows[*].id')` |
| check a key exists (null-safe) | `values($d, $path) !== []` |
| set / create | `$d = withValue($d, 'a.b', $v)` |
| set on every child | `$d = withValue($d, 'rows[*].flag', $v)` |
| delete | `$d = withoutValue($d, 'a.b')` |
| address a key with `.`/`]`/`*` in it | escape: `a\.b`, `[a\]b]`, `[\*]` |
