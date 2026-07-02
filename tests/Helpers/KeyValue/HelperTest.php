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
			'missing is omitted'          => [(object) [], 'a', []],
			'object wildcard'             => [(object) ['a' => 1, 'b' => 2], '*', ['a' => 1, 'b' => 2]],
			'array wildcard keys'         => [(object) ['x' => [10, 20]], 'x[*]', ['x[0]' => 10, 'x[1]' => 20]],
			'nested wildcard'             => [
				(object) ['rows' => [(object) ['n' => 1], (object) ['n' => 2]]],
				'rows[*].n',
				['rows[0].n' => 1, 'rows[1].n' => 2],
			],
			'wildcard on wrong type'      => [(object) ['a' => 1], '[*]', []],
		];
	}

	// ------------------------------------------------------------ withValue()

	#[DataProvider('withValueProvider')]
	public function testWithValue(array|object $data, string $path, mixed $value, string $expectedJson): void
	{
		$this->assertSame($expectedJson, json_encode($this->helper->withValue($data, $path, $value)));
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
		];
	}

	public function testWithoutValueDoesNotMutateOriginal(): void
	{
		$data = (object) ['a' => 1, 'b' => 2];
		$this->helper->withoutValue($data, 'a');

		$this->assertSame(1, $data->a);
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
