<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedString;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameter::class)]
final class ParameterTest extends TestCase
{
	public function testExposesNameAndTokenValue(): void
	{
		$name = new Token('charset');
		$value = new Token('utf-8');

		$parameter = new Parameter($name, $value);

		$this->assertSame($name, $parameter->name());
		$this->assertSame($value, $parameter->value());
	}

	public function testExposesQuotedStringValue(): void
	{
		$value = new QuotedString('utf 8');

		$parameter = new Parameter(new Token('charset'), $value);

		$this->assertSame($value, $parameter->value());
	}
}
