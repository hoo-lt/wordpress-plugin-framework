<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameters::class)]
final class ParametersTest extends TestCase
{
	public function testFindsParameterByName(): void
	{
		$charset = new Parameter(new Token('charset'), new Token('utf-8'));

		$this->assertSame($charset, (new Parameters([$charset]))->parameter('charset'));
	}

	/**
	 * parameter names are case-insensitive per RFC 9110 section 5.6.6
	 */
	public function testLookupNameIsCaseInsensitive(): void
	{
		$charset = new Parameter(new Token('charset'), new Token('utf-8'));

		$this->assertSame($charset, (new Parameters([$charset]))->parameter('CharSet'));
	}

	public function testStoredNameIsCaseInsensitive(): void
	{
		$charset = new Parameter(new Token('CharSet'), new Token('utf-8'));

		$this->assertSame($charset, (new Parameters([$charset]))->parameter('charset'));
	}

	public function testMissingParameterIsNull(): void
	{
		$level = new Parameter(new Token('level'), new Token('1'));

		$this->assertNull((new Parameters([$level]))->parameter('charset'));
	}

	public function testEmptyParameters(): void
	{
		$parameters = new Parameters([]);

		$this->assertCount(0, $parameters);
		$this->assertNull($parameters->parameter('charset'));
	}

	public function testFirstDuplicateWins(): void
	{
		$first = new Parameter(new Token('charset'), new Token('utf-8'));
		$second = new Parameter(new Token('charset'), new Token('ascii'));

		$this->assertSame($first, (new Parameters([$first, $second]))->parameter('charset'));
	}

	public function testCountsAndIteratesInOrder(): void
	{
		$charset = new Parameter(new Token('charset'), new Token('utf-8'));
		$level = new Parameter(new Token('level'), new Token('1'));

		$parameters = new Parameters([$charset, $level]);

		$this->assertCount(2, $parameters);
		$this->assertSame([$charset, $level], iterator_to_array($parameters));
	}
}
