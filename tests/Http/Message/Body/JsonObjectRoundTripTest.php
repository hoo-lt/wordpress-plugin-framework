<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Message\Body;

use Hoo\WordPressPluginFramework\Helpers\KeyValue\Helper;
use Hoo\WordPressPluginFramework\Http\Coders\Json\Coder;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * The "json objects too" contract: a JSON object decodes to a queryable
 * stdClass (dot-addressable), a JSON array to an array (bracket-addressable),
 * and both re-encode with shape fidelity — empty object stays {}.
 */
#[CoversNothing]
final class JsonObjectRoundTripTest extends TestCase
{
	private Coder $coder;
	private Helper $helper;

	protected function setUp(): void
	{
		$this->coder = new Coder();
		$this->helper = new Helper();
	}

	public function testJsonObjectDecodesToQueryableObject(): void
	{
		$body = $this->coder->decode('{"user":{"name":"Bob"},"tags":["x","y"]}');

		$this->assertIsObject($body);
		$this->assertSame('Bob', $this->helper->value($body, 'user.name'));
		$this->assertSame('x', $this->helper->value($body, 'tags[0]'));
	}

	public function testEmptyObjectRoundTripsAsObject(): void
	{
		$this->assertSame('{}', $this->coder->encode($this->coder->decode('{}')));
	}

	public function testEmptyArrayRoundTripsAsArray(): void
	{
		$this->assertSame('[]', $this->coder->encode($this->coder->decode('[]')));
	}

	public function testMixedShapeRoundTripsExactly(): void
	{
		$json = '{"a":{"b":[]},"c":[1,2]}';

		$this->assertSame($json, $this->coder->encode($this->coder->decode($json)));
	}

	public function testQueriedThenModifiedRoundTrips(): void
	{
		$body = $this->coder->decode('{"user":{"name":"Bob"}}');

		$body = $this->helper->withValue($body, 'user.age', 30);

		$this->assertSame('{"user":{"name":"Bob","age":30}}', $this->coder->encode($body));
	}

	public function testScalarJsonDecodesToScalar(): void
	{
		$this->assertSame(5, $this->coder->decode('5'));
		$this->assertNull($this->coder->decode('null'));
	}
}
