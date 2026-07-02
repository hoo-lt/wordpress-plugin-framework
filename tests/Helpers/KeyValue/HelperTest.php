<?php

namespace Hoo\WordPressPluginFramework\Tests\Helpers\KeyValue;

use Hoo\WordPressPluginFramework\Helpers\KeyValue\Helper;
use Hoo\WordPressPluginFramework\Helpers\KeyValue\HelperException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Helper::class)]
final class HelperTest extends TestCase
{
	private Helper $helper;

	protected function setUp(): void
	{
		$this->helper = new Helper();
	}

	// ---------------------------------------------------------------- value()

	#[DataProvider('valueProvider')]
	public function testValue(array|object $data, string $path, mixed $expected): void
	{
		$this->assertSame($expected, $this->helper->value($data, $path));
	}

	public static function valueProvider(): array
	{
		$obj = (object) ['a' => 1, 'items' => [10, 20]];

		return [
			'object property'              => [$obj, 'a', 1],
			'array index'                  => [$obj, 'items[1]', 20],
			'root array index'             => [[10, 20, 30], '[2]', 30],
			'array data via brackets'      => [['user' => ['name' => 'Bob']], '[user][name]', 'Bob'],
			'nested mixed'                 => [(object) ['items' => [(object) ['n' => 5]]], 'items[0].n', 5],
			'property on array is a miss'  => [[1, 2], 'name', null],
			'index on object is a miss'    => [$obj, '[a]', null],
			'missing key'                  => [$obj, 'nope', null],
			'descend into scalar is miss'  => [$obj, 'a.b', null],
			'found null returns null'      => [(object) ['a' => null], 'a', null],
			'wildcard returns a list'      => [(object) ['x' => [10, 20]], 'x[*]', [10, 20]],
			'escaped dot in key'           => [(object) ['items' => ['a.b' => 5]], 'items[a\.b]', 5],
			'escaped star is literal'      => [(object) ['x' => ['*' => 7]], 'x[\*]', 7],
			'container leaf returned as-is'   => [$obj, 'items', [10, 20]],
			'wildcard zero matches is []'     => [(object) ['x' => []], 'x[*]', []],
			'wildcard on missing is []'       => [(object) [], 'x[*]', []],
			'object wildcard on array is []'  => [(object) ['x' => [1, 2]], 'x.*', []],
			'double wildcard flattens'        => [
				(object) ['rows' => [(object) ['tags' => [1, 2]], (object) ['tags' => [3]]]],
				'rows[*].tags[*]',
				[1, 2, 3],
			],
			'escaped closing bracket in key'  => [['a]b' => 7], '[a\]b]', 7],
			'escaped backslash in key'        => [['a\b' => 8], '[a\\\b]', 8],
			'literal ] in property'           => [(object) ['a]b' => 4], 'a]b', 4],
			'property with literal dot'       => [(object) ['a.b' => 3], 'a\.b', 3],
			'whitespace is literal'           => [(object) [' a' => 2], ' a', 2],
			'utf-8 property and key'          => [(object) ['ключ' => ['значение' => 6]], 'ключ[значение]', 6],
			'numeric property on object'      => [(object) ['0' => 'x'], '0', 'x'],
			'found false is not a miss'       => [(object) ['a' => false], 'a', false],
			'found zero is not a miss'        => [(object) ['a' => 0], 'a', 0],
			'found empty string is not a miss' => [(object) ['a' => ''], 'a', ''],
			'found "0" is not a miss'         => [(object) ['a' => '0'], 'a', '0'],
			'escaped star key mid-path'       => [(object) ['x' => ['*' => (object) ['y' => 5]]], 'x[\*].y', 5],
			'star-prefixed key is literal'    => [(object) ['*x' => 3, 'y' => 4], '*x', 3],
			'wildcard list keeps found nulls' => [
				(object) ['rows' => [(object) ['n' => null], (object) ['n' => 2]]],
				'rows[*].n',
				[null, 2],
			],
		];
	}

	// --------------------------------------------------------------- values()

	#[DataProvider('valuesProvider')]
	public function testValues(array|object $data, string $path, array $expected): void
	{
		$this->assertSame($expected, $this->helper->values($data, $path));
	}

	public static function valuesProvider(): array
	{
		return [
			'present key'                 => [(object) ['a' => 1], 'a', ['a' => 1]],
			'found null is present'       => [(object) ['a' => null], 'a', ['a' => null]],
			'found false is present'      => [(object) ['a' => false], 'a', ['a' => false]],
			'null array value is present' => [['a' => null], '[a]', ['[a]' => null]],
			'missing is omitted'          => [(object) [], 'a', []],
			'object wildcard'             => [(object) ['a' => 1, 'b' => 2], '*', ['a' => 1, 'b' => 2]],
			'array wildcard keys'         => [(object) ['x' => [10, 20]], 'x[*]', ['x[0]' => 10, 'x[1]' => 20]],
			'nested wildcard'             => [
				(object) ['rows' => [(object) ['n' => 1], (object) ['n' => 2]]],
				'rows[*].n',
				['rows[0].n' => 1, 'rows[1].n' => 2],
			],
			'wildcard on wrong type'      => [(object) ['a' => 1], '[*]', []],
			'root array wildcard keys'    => [['x', 'y'], '[*]', ['[0]' => 'x', '[1]' => 'y']],
			'double wildcard keys'        => [
				(object) ['rows' => [(object) ['tags' => [1, 2]], (object) ['tags' => [3]]]],
				'rows[*].tags[*]',
				['rows[0].tags[0]' => 1, 'rows[0].tags[1]' => 2, 'rows[1].tags[0]' => 3],
			],
			'partial wildcard misses omitted' => [
				(object) ['rows' => [(object) ['n' => 1], (object) []]],
				'rows[*].n',
				['rows[0].n' => 1],
			],
			'container value returned whole'  => [(object) ['a' => ['b' => 1]], 'a', ['a' => ['b' => 1]]],
			'reserved array keys escaped'     => [
				(object) ['x' => ['*' => 1, 'a]b' => 2, 'a\b' => 3, 'a.b' => 4]],
				'x[*]',
				['x[\*]' => 1, 'x[a\]b]' => 2, 'x[a\\\b]' => 3, 'x[a.b]' => 4],
			],
			'reserved property keys escaped'  => [
				(object) ['*' => 1, 'a.b' => 2, 'a[b' => 3, 'a]b' => 4],
				'*',
				['\*' => 1, 'a\.b' => 2, 'a\[b' => 3, 'a]b' => 4],
			],
		];
	}

	// ------------------------------------------------------------ withValue()

	#[DataProvider('withValueProvider')]
	public function testWithValue(array|object $data, string $path, mixed $value, string $expectedJson): void
	{
		$this->assertSame($expectedJson, json_encode($this->helper->withValue($data, $path, $value), JSON_UNESCAPED_UNICODE));
	}

	public static function withValueProvider(): array
	{
		return [
			'set existing property'        => [(object) ['a' => 1], 'a', 9, '{"a":9}'],
			'set existing index'           => [(object) ['x' => [1, 2]], 'x[0]', 9, '{"x":[9,2]}'],
			'append array index'           => [(object) ['x' => [1]], 'x[1]', 2, '{"x":[1,2]}'],
			'create object chain'          => [new stdClass(), 'a.b', 9, '{"a":{"b":9}}'],
			'create array element'         => [new stdClass(), 'a[0]', 9, '{"a":[9]}'],
			'deep mixed create'            => [new stdClass(), 'a[0].b', 9, '{"a":[{"b":9}]}'],
			'numeric property (dot)'       => [new stdClass(), 'a.0', 9, '{"a":{"0":9}}'],
			'numeric index (bracket)'      => [new stdClass(), 'a[0]', 9, '{"a":[9]}'],
			'empty array root + index'     => [[], '[0]', 9, '[9]'],
			'wildcard object set'          => [(object) ['a' => 1, 'b' => 2], '*', 0, '{"a":0,"b":0}'],
			'wildcard array set'           => [(object) ['x' => [1, 2]], 'x[*]', 0, '{"x":[0,0]}'],
			'nested object wildcard set'   => [(object) ['a' => (object) ['x' => 1, 'y' => 2]], 'a.*', 0, '{"a":{"x":0,"y":0}}'],
			'replace container leaf'       => [(object) ['a' => (object) ['b' => 1]], 'a', 9, '{"a":9}'],
			'set null is a real value'     => [(object) ['a' => 1], 'a', null, '{"a":null}'],
			'gap index degrades list'      => [(object) ['x' => [1]], 'x[5]', 9, '{"x":{"0":1,"5":9}}'],
			'deep object chain'            => [new stdClass(), 'a.b.c.d', 9, '{"a":{"b":{"c":{"d":9}}}}'],
			'wildcard on empty array'      => [(object) ['x' => []], 'x[*]', 0, '{"x":[]}'],
			'wildcard materializes empty'  => [new stdClass(), 'x[*]', 0, '{"x":[]}'],
			'wildcard creates missing leaf in children' => [
				(object) ['rows' => [(object) ['f' => 1], new stdClass()]],
				'rows[*].f',
				0,
				'{"rows":[{"f":0},{"f":0}]}',
			],
			'write via escaped star key'   => [(object) ['x' => ['*' => 1]], 'x[\*]', 9, '{"x":{"*":9}}'],
			'write via escaped star property' => [(object) ['*' => 1], '\*', 9, '{"*":9}'],
			'write via escaped bracket key' => [(object) ['x' => ['a]b' => 1]], 'x[a\]b]', 9, '{"x":{"a]b":9}}'],
			'write utf-8 path'             => [new stdClass(), 'ключ[значение]', 9, '{"ключ":{"значение":9}}'],
			'write numeric object property' => [(object) ['0' => 'x'], '0', 9, '{"0":9}'],
			'double wildcard write'        => [(object) ['x' => [[1, 2], [3]]], 'x[*][*]', 0, '{"x":[[0,0],[0]]}'],
			'wildcard on empty object'     => [new stdClass(), '*', 0, '{}'],
			'deep wildcard materializes empty' => [new stdClass(), 'x[*].y', 0, '{"x":[]}'],
			'wildcard on assoc preserves keys' => [(object) ['x' => ['a' => 1, 'b' => 2]], 'x[*]', 0, '{"x":{"a":0,"b":0}}'],
			'container as written value'   => [new stdClass(), 'a', ['b' => 1], '{"a":{"b":1}}'],
		];
	}

	#[DataProvider('writeThrowProvider')]
	public function testWithValueThrowsOnConflict(array|object $data, string $path): void
	{
		$this->expectException(HelperException::class);
		$this->helper->withValue($data, $path, 9);
	}

	public static function writeThrowProvider(): array
	{
		return [
			'property on array'              => [[1, 2], 'name'],
			'index on object'                => [new stdClass(), '[0]'],
			'descend into scalar'            => [(object) ['a' => 5], 'a.b'],
			'empty array root, object path'  => [[], 'a'],
			'mid-path mismatch'              => [(object) ['a' => [1, 2]], 'a.b'],
			'bracket into object mid-path'   => [(object) ['a' => new stdClass()], 'a[0]'],
			'wildcard over scalar children'  => [(object) ['rows' => [1, 2]], 'rows[*].b'],
			'object wildcard on array'       => [(object) ['x' => [1, 2]], 'x.*'],
			'descend into null intermediate' => [(object) ['a' => null], 'a.b'],
		];
	}

	public function testWithValueDoesNotMutateOriginal(): void
	{
		$data = (object) ['a' => 1];
		$this->helper->withValue($data, 'a', 9);

		$this->assertSame(1, $data->a);
	}

	public function testWithValueClonesNestedContainers(): void
	{
		$inner = (object) ['b' => 1];
		$data = (object) ['a' => $inner];

		$result = $this->helper->withValue($data, 'a.b', 9);

		$this->assertSame(1, $inner->b, 'nested original must be untouched');
		$this->assertSame(9, $result->a->b, 'result must carry the new value');
	}

	// --------------------------------------------------------- withoutValue()

	#[DataProvider('withoutValueProvider')]
	public function testWithoutValue(array|object $data, string $path, string $expectedJson): void
	{
		$this->assertSame($expectedJson, json_encode($this->helper->withoutValue($data, $path)));
	}

	public static function withoutValueProvider(): array
	{
		return [
			'remove property'              => [(object) ['a' => 1, 'b' => 2], 'a', '{"b":2}'],
			'remove last leaves empty obj' => [(object) ['a' => 1], 'a', '{}'],
			'remove index reindexes list'  => [(object) ['x' => [1, 2, 3]], 'x[1]', '{"x":[1,3]}'],
			'remove leaves empty array'    => [(object) ['x' => [1]], 'x[0]', '{"x":[]}'],
			'remove assoc key no reindex'  => [(object) ['x' => ['a' => 1, 'b' => 2]], 'x[a]', '{"x":{"b":2}}'],
			'missing path is a no-op'      => [(object) ['a' => 1], 'b', '{"a":1}'],
			'type mismatch is a no-op'     => [[1, 2], 'name', '[1,2]'],
			'wildcard clears object'       => [(object) ['a' => 1, 'b' => 2], '*', '{}'],
			'wildcard clears array'        => [(object) ['x' => [1, 2]], 'x[*]', '{"x":[]}'],
			'deep nested remove'           => [
				(object) ['a' => (object) ['b' => (object) ['c' => 1, 'd' => 2]]],
				'a.b.c',
				'{"a":{"b":{"d":2}}}',
			],
			'wildcard mid-path remove'     => [
				(object) ['rows' => [(object) ['n' => 1, 'k' => 2], (object) ['n' => 3]]],
				'rows[*].n',
				'{"rows":[{"k":2},{}]}',
			],
			'missing under wildcard is a no-op' => [
				(object) ['rows' => [(object) ['n' => 1], (object) []]],
				'rows[*].n',
				'{"rows":[{},{}]}',
			],
			'gapped int keys do not reindex'    => [(object) ['x' => [0 => 'a', 2 => 'c']], 'x[0]', '{"x":{"2":"c"}}'],
			'object wildcard on array is a no-op' => [(object) ['x' => [1, 2]], 'x.*', '{"x":[1,2]}'],
			'root array wildcard clears'   => [['a', 'b'], '[*]', '[]'],
			'wildcard skips mismatched children' => [
				(object) ['rows' => [1, (object) ['n' => 2]]],
				'rows[*].n',
				'{"rows":[1,{}]}',
			],
			'remove via escaped star key'  => [(object) ['x' => ['*' => 1, 'a' => 2]], 'x[\*]', '{"x":{"a":2}}'],
			'wildcard clear on assoc flips shape' => [(object) ['x' => ['a' => 1, 'b' => 2]], 'x[*]', '{"x":[]}'],
			'double wildcard remove'       => [
				(object) ['rows' => [(object) ['tags' => [1, 2]], (object) ['tags' => [3]]]],
				'rows[*].tags[*]',
				'{"rows":[{"tags":[]},{"tags":[]}]}',
			],
			'removes null-valued property'  => [(object) ['a' => null, 'b' => 2], 'a', '{"b":2}'],
			'removes null-valued array key' => [(object) ['x' => ['a' => null, 'b' => 2]], 'x[a]', '{"x":{"b":2}}'],
			'null intermediate is a no-op'  => [(object) ['a' => null], 'a.b', '{"a":null}'],
			'removes numeric object property' => [(object) ['0' => 'x', 'a' => 1], '0', '{"a":1}'],
			'object wildcard with rest'     => [
				(object) ['r1' => (object) ['n' => 1, 'k' => 2], 'r2' => (object) ['n' => 3]],
				'*.n',
				'{"r1":{"k":2},"r2":{}}',
			],
		];
	}

	public function testWithoutValueDoesNotMutateOriginal(): void
	{
		$data = (object) ['a' => 1, 'b' => 2];
		$this->helper->withoutValue($data, 'a');

		$this->assertSame(1, $data->a);
	}

	public function testWithoutValueClonesNestedContainers(): void
	{
		$inner = (object) ['b' => 1, 'c' => 2];
		$data = (object) ['a' => $inner];

		$result = $this->helper->withoutValue($data, 'a.b');

		$this->assertSame(1, $inner->b, 'nested original must be untouched');
		$this->assertFalse(property_exists($result->a, 'b'));
	}

	public function testWildcardWriteDoesNotMutateOriginal(): void
	{
		$row = (object) ['n' => 1];
		$data = (object) ['rows' => [$row]];

		$this->helper->withValue($data, 'rows[*].n', 0);

		$this->assertSame(1, $row->n);
	}

	public function testWildcardRemoveDoesNotMutateOriginal(): void
	{
		$row = (object) ['n' => 1, 'k' => 2];
		$data = (object) ['rows' => [$row]];

		$this->helper->withoutValue($data, 'rows[*].n');

		$this->assertSame(1, $row->n);
	}

	// ------------------------------------------------------------- invariants

	public function testUntouchedSiblingContainersAreShared(): void
	{
		$shared = (object) ['s' => 1];
		$data = (object) ['a' => (object) ['b' => 1], 'shared' => $shared];

		$result = $this->helper->withValue($data, 'a.b', 9);

		$this->assertNotSame($data, $result, 'root must be cloned');
		$this->assertNotSame($data->a, $result->a, 'container on the written path must be cloned');
		$this->assertSame($shared, $result->shared, 'container off the written path must be shared, not copied');
	}

	public function testRemoveSharesUntouchedSiblings(): void
	{
		$shared = (object) ['s' => 1];
		$data = (object) ['a' => (object) ['b' => 1, 'c' => 2], 'shared' => $shared];

		$result = $this->helper->withoutValue($data, 'a.b');

		$this->assertNotSame($data->a, $result->a, 'container on the removed path must be cloned');
		$this->assertSame($shared, $result->shared, 'container off the removed path must be shared, not copied');
	}

	public function testReadReturnsTheActualInstance(): void
	{
		$inner = (object) ['b' => 1];
		$data = (object) ['a' => $inner];

		$this->assertSame($inner, $this->helper->value($data, 'a'), 'reads must not clone');
	}

	public function testWrittenContainerIsStoredAsIs(): void
	{
		$value = (object) ['b' => 1];

		$result = $this->helper->withValue(new stdClass(), 'a', $value);

		$this->assertSame($value, $result->a, 'written values must be stored by identity, not cloned');
	}

	public function testThrowingWriteLeavesOriginalUntouched(): void
	{
		$row = (object) ['n' => 1];
		$data = (object) ['rows' => [$row, 5]];

		try {
			$this->helper->withValue($data, 'rows[*].n', 0);
			$this->fail('expected HelperException');
		} catch (HelperException) {
		}

		$this->assertSame(1, $row->n, 'partially applied write must not leak into the original');
		$this->assertSame(5, $data->rows[1]);
	}

	public function testValuesKeysAreRoundTrippablePaths(): void
	{
		$data = (object) [
			'rows' => [(object) ['n' => 1], (object) ['n' => 2]],
			'map' => (object) ['x' => ['a' => 5, 'b.c' => 6, '*' => 7, 'a]b' => 8, 'a\b' => 9]],
			'*' => 10,
			'p.q' => 11,
			'p[q' => 12,
		];

		foreach (['rows[*].n', 'map.*', 'map.x[*]', '*'] as $wildcard) {
			$values = $this->helper->values($data, $wildcard);
			$this->assertNotSame([], $values);

			foreach ($values as $resolved => $value) {
				$this->assertSame(
					$value,
					$this->helper->value($data, $resolved),
					sprintf('resolved path "%s" from "%s" must address the same value', $resolved, $wildcard)
				);
			}
		}
	}

	public function testWriteThenReadReturnsWritten(): void
	{
		$result = $this->helper->withValue(new stdClass(), 'a[0].b', 9);

		$this->assertSame(9, $this->helper->value($result, 'a[0].b'));
	}

	public function testObjectInsideArrayIsClonedOnWrite(): void
	{
		$inner = (object) ['b' => 1];
		$data = ['o' => $inner];

		$result = $this->helper->withValue($data, '[o].b', 9);

		$this->assertSame(1, $inner->b, 'object referenced from the original array must be untouched');
		$this->assertSame(9, $result['o']->b);
		$this->assertNotSame($inner, $result['o']);
	}

	public function testValuesKeysAreWritablePaths(): void
	{
		$data = (object) ['map' => (object) ['x' => ['a' => 5, '*' => 7, 'a]b' => 8, 'a\b' => 9]]];

		foreach ($this->helper->values($data, 'map.x[*]') as $resolved => $value) {
			$written = $this->helper->withValue($data, $resolved, 'W');

			$this->assertSame(
				'W',
				$this->helper->value($written, $resolved),
				sprintf('resolved path "%s" must be writable back to the same address', $resolved)
			);
		}
	}

	public function testRemoveThenValuesOmits(): void
	{
		$data = (object) ['a' => 1, 'b' => 2];

		$result = $this->helper->withoutValue($data, 'a');

		$this->assertSame([], $this->helper->values($result, 'a'));
		$this->assertSame(['b' => 2], $this->helper->values($result, 'b'));
	}

	// ---------------------------------------------------------- path parsing

	#[DataProvider('invalidPathProvider')]
	public function testInvalidPathThrows(string $path): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->helper->value(new stdClass(), $path);
	}

	public static function invalidPathProvider(): array
	{
		return [
			'empty path'            => [''],
			'leading dot'           => ['.a'],
			'trailing dot'          => ['a.'],
			'double dot'            => ['a..b'],
			'empty index'           => ['[]'],
			'unbalanced bracket'    => ['[a'],
			'dangling escape'       => ['a\\'],
			'bare token after index'=> ['[0]x'],
		];
	}
}
