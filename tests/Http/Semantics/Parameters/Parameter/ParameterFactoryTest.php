<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringFactoryException;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringInterface;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\TokenException;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\TokenInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * the factory takes the wire format:
 * parameter = parameter-name "=" parameter-value per RFC 9110 section 5.6.6
 */
#[CoversClass(ParameterFactory::class)]
#[CoversClass(Parameter::class)]
final class ParameterFactoryTest extends TestCase
{
	private ParameterFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new ParameterFactory(
			new QuotedStringFactory(),
		);
	}

	public function testParsesTokenValue(): void
	{
		$parameter = $this->factory->create('charset=utf-8');

		$this->assertSame('charset', (string) $parameter->name());
		$this->assertInstanceOf(TokenInterface::class, $parameter->value());
		$this->assertSame('utf-8', (string) $parameter->value());
	}

	public function testLowercasesNameButPreservesValueCase(): void
	{
		$parameter = $this->factory->create('CharSet=UTF-8');

		$this->assertSame('charset', (string) $parameter->name());
		$this->assertSame('UTF-8', (string) $parameter->value());
	}

	public function testDecodesQuotedStringValue(): void
	{
		$parameter = $this->factory->create('title="a \\"b\\" c"');

		$this->assertInstanceOf(QuotedStringInterface::class, $parameter->value());
		$this->assertSame('a "b" c', (string) $parameter->value());
	}

	/**
	 * "=" is qdtext, so it is only a name/value separator outside quotes
	 */
	public function testEqualsInsideQuotedValue(): void
	{
		$parameter = $this->factory->create('filename="a=b"');

		$this->assertSame('filename', (string) $parameter->name());
		$this->assertSame('a=b', (string) $parameter->value());
	}

	public function testEmptyQuotedStringValueIsPresentButEmpty(): void
	{
		$this->assertSame('', (string) $this->factory->create('charset=""')->value());
	}

	#[DataProvider('invalidTokenPartProvider')]
	public function testRejectsInvalidNameOrTokenValue(string $wire): void
	{
		$this->expectException(TokenException::class);

		$this->factory->create($wire);
	}

	public static function invalidTokenPartProvider(): array
	{
		return [
			'no equals (parameter = parameter-name "=" parameter-value)' => ['charset'],
			'empty name' => ['=utf-8'],
			'empty token value (1*tchar)' => ['charset='],
			'space in name' => ['char set=utf-8'],
			'space before equals (no BWS in parameter)' => ['charset =utf-8'],
			'space after equals (no BWS in parameter)' => ['charset= utf-8'],
			'equals inside token value' => ['a=b=c'],
			'quote inside token value' => ['a=b"c'],
			'trailing content after nothing quotable' => ['a=b c'],
		];
	}

	#[DataProvider('invalidQuotedValueProvider')]
	public function testRejectsInvalidQuotedStringValue(string $wire): void
	{
		$this->expectException(QuotedStringFactoryException::class);

		$this->factory->create($wire);
	}

	public static function invalidQuotedValueProvider(): array
	{
		return [
			'unterminated quoted-string' => ['a="unterminated'],
			'content after closing quote' => ['a="x"y'],
			'lone quote value' => ['a="'],
		];
	}
}
