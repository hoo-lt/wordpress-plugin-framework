<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameter;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\Parameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameter::class)]
final class ParameterTest extends TestCase
{
	public function testExposesNameAndValueVerbatim(): void
	{
		$parameter = new Parameter('charset', 'UTF-8');

		$this->assertSame('charset', $parameter->name());
		$this->assertSame('UTF-8', $parameter->value());
	}

	public function testAllowsEmptyValue(): void
	{
		$parameter = new Parameter('title', '');

		$this->assertSame('', $parameter->value());
	}
}
