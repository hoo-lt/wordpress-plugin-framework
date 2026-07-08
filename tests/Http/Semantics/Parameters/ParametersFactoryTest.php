<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringFactoryException;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\TokenException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * the factory takes the wire format:
 * parameters = *( OWS ";" OWS [ parameter ] ) per RFC 9110 section 5.6.6,
 * here as the substring following the first ";" of the field value
 */
#[CoversClass(ParametersFactory::class)]
#[CoversClass(Parameters::class)]
#[CoversClass(ParameterFactory::class)]
#[CoversClass(Parameter::class)]
final class ParametersFactoryTest extends TestCase
{
	private ParametersFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new ParametersFactory(
			new ParameterFactory(
				new QuotedStringFactory(),
			),
		);
	}

	public function testParsesParameterList(): void
	{
		$parameters = $this->factory->create('charset=utf-8; level=1');

		$this->assertCount(2, $parameters);
		$this->assertSame('utf-8', (string) $parameters->parameter('charset')->value());
		$this->assertSame('1', (string) $parameters->parameter('level')->value());
	}

	public function testEmptyWireIsEmptyList(): void
	{
		$this->assertCount(0, $this->factory->create(''));
	}

	#[DataProvider('owsProvider')]
	public function testTrimsOptionalWhitespaceAroundSeparators(string $wire): void
	{
		$parameters = $this->factory->create($wire);

		$this->assertCount(2, $parameters);
		$this->assertSame('utf-8', (string) $parameters->parameter('charset')->value());
		$this->assertSame('1', (string) $parameters->parameter('level')->value());
	}

	public static function owsProvider(): array
	{
		return [
			'no whitespace' => ['charset=utf-8;level=1'],
			'space after separator' => ['charset=utf-8; level=1'],
			'space before separator' => ['charset=utf-8 ;level=1'],
			'tabs around separator' => ["charset=utf-8\t;\tlevel=1"],
			'leading and trailing ows' => [" \tcharset=utf-8; level=1\t "],
		];
	}

	/**
	 * the [ parameter ] in the grammar is optional, so empty slots are legal
	 */
	public function testEmptySlotsBetweenSeparatorsAreSkipped(): void
	{
		$parameters = $this->factory->create('; charset=utf-8 ; ; level=1;');

		$this->assertCount(2, $parameters);
		$this->assertSame('utf-8', (string) $parameters->parameter('charset')->value());
	}

	public function testSkippedSlotsLeaveNoGapsInIteration(): void
	{
		$parameters = $this->factory->create('; charset=utf-8 ; ; level=1;');

		$this->assertSame([0, 1], array_keys(iterator_to_array($parameters)));
	}

	public function testSemicolonInsideQuotedStringIsNotASeparator(): void
	{
		$parameters = $this->factory->create('title="a;b"; charset=utf-8');

		$this->assertCount(2, $parameters);
		$this->assertSame('a;b', (string) $parameters->parameter('title')->value());
		$this->assertSame('utf-8', (string) $parameters->parameter('charset')->value());
	}

	public function testEscapedQuoteInsideQuotedStringKeepsSplitIntact(): void
	{
		$parameters = $this->factory->create('title="a\\";b"; level=1');

		$this->assertCount(2, $parameters);
		$this->assertSame('a";b', (string) $parameters->parameter('title')->value());
		$this->assertSame('1', (string) $parameters->parameter('level')->value());
	}

	public function testSplitterReentersAfterQuotedString(): void
	{
		$parameters = $this->factory->create('a="1;2"; b="3;4"');

		$this->assertCount(2, $parameters);
		$this->assertSame('1;2', (string) $parameters->parameter('a')->value());
		$this->assertSame('3;4', (string) $parameters->parameter('b')->value());
	}

	public function testEscapedBackslashBeforeClosingQuoteDoesNotSwallowSeparator(): void
	{
		$parameters = $this->factory->create('a="x\\\\"; b=1');

		$this->assertCount(2, $parameters);
		$this->assertSame('x\\', (string) $parameters->parameter('a')->value());
		$this->assertSame('1', (string) $parameters->parameter('b')->value());
	}

	/**
	 * ows-only input corresponds to "type/subtype; " on the wire,
	 * which the grammar allows as an empty parameter slot
	 */
	public function testOwsOnlyWireIsEmptyList(): void
	{
		$this->assertCount(0, $this->factory->create(" \t "));
	}

	public function testUnterminatedQuotedStringIsRejected(): void
	{
		$this->expectException(QuotedStringFactoryException::class);

		$this->factory->create('a="x; b=1');
	}

	public function testValuelessParameterIsRejected(): void
	{
		$this->expectException(TokenException::class);

		$this->factory->create('charset=utf-8; level');
	}

	#[DataProvider('nonOwsWhitespaceProvider')]
	public function testOnlySpaceAndTabAreOptionalWhitespace(string $wire): void
	{
		$this->expectException(TokenException::class);

		$this->factory->create($wire);
	}

	public static function nonOwsWhitespaceProvider(): array
	{
		return [
			'whitespace around equals (no BWS in parameter)' => ['charset = utf-8'],
			'vertical tab is not ows' => ["charset=utf-8\x0B"],
			'line feed is not ows' => ["\ncharset=utf-8"],
			'carriage return is not ows' => ["charset=utf-8\r"],
		];
	}
}
