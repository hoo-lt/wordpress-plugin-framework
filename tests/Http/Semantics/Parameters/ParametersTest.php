<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameters::class)]
#[CoversClass(Parameter::class)]
final class ParametersTest extends TestCase
{
	public function testParameterLookupIsCaseInsensitive(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$parameters = new Parameters([$charset, new Parameter('boundary', 'xyz')]);

		// stored names are already folded; the lookup folds the query too (RFC 9110 §5.6.6)
		$this->assertSame($charset, $parameters->parameter('charset'));
		$this->assertSame($charset, $parameters->parameter('CHARSET'));
		$this->assertSame($charset, $parameters->parameter('ChArSeT'));
	}

	public function testMissingParameterIsNull(): void
	{
		$parameters = new Parameters([new Parameter('charset', 'utf-8')]);

		$this->assertNull($parameters->parameter('boundary'));
	}

	public function testDuplicateNameReturnsFirst(): void
	{
		$first = new Parameter('a', '1');
		$parameters = new Parameters([$first, new Parameter('a', '2')]);

		$this->assertSame($first, $parameters->parameter('a'));
	}

	public function testCountAndIterationPreserveOrder(): void
	{
		$a = new Parameter('a', '1');
		$b = new Parameter('b', '2');
		$parameters = new Parameters([$a, $b]);

		$this->assertCount(2, $parameters);
		$this->assertSame([$a, $b], iterator_to_array($parameters));
	}

	public function testEmptyParameters(): void
	{
		$parameters = new Parameters([]);

		$this->assertCount(0, $parameters);
		$this->assertNull($parameters->parameter('anything'));
		$this->assertSame([], iterator_to_array($parameters));
	}

	public function testParameterExposesNameAndValueVerbatim(): void
	{
		$parameter = new Parameter('charset', 'UTF-8');

		$this->assertSame('charset', $parameter->name());
		$this->assertSame('UTF-8', $parameter->value());
	}

	public function testParameterAllowsEmptyValue(): void
	{
		$parameter = new Parameter('title', '');

		$this->assertSame('', $parameter->value());
	}
}
