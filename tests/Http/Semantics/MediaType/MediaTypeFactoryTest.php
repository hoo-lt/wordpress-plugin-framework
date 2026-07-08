<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaTypeFactory;
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
 * media-type = type "/" subtype parameters per RFC 9110 section 8.3.1
 */
#[CoversClass(MediaTypeFactory::class)]
#[CoversClass(MediaType::class)]
#[CoversClass(ParametersFactory::class)]
#[CoversClass(Parameters::class)]
#[CoversClass(ParameterFactory::class)]
#[CoversClass(Parameter::class)]
final class MediaTypeFactoryTest extends TestCase
{
	private MediaTypeFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new MediaTypeFactory(
			new ParametersFactory(
				new ParameterFactory(
					new QuotedStringFactory(),
				),
			),
		);
	}

	public function testParsesEssence(): void
	{
		$mediaType = $this->factory->create('application/json');

		$this->assertSame('application', $mediaType->type());
		$this->assertSame('json', $mediaType->subtype());
		$this->assertCount(0, $mediaType->parameters());
		$this->assertNull($mediaType->charset());
	}

	/**
	 * type and subtype are case-insensitive per RFC 9110 section 8.3.1
	 */
	public function testLowercasesTypeAndSubtype(): void
	{
		$mediaType = $this->factory->create('Application/JSON');

		$this->assertSame('application', $mediaType->type());
		$this->assertSame('json', $mediaType->subtype());
	}

	#[DataProvider('charsetProvider')]
	public function testParsesCharset(string $wire, string $charset): void
	{
		$this->assertSame($charset, $this->factory->create($wire)->charset());
	}

	public static function charsetProvider(): array
	{
		return [
			'token value' => ['text/html; charset=utf-8', 'utf-8'],
			'no ows after separator' => ['text/html;charset=utf-8', 'utf-8'],
			'ows before separator' => ['text/html ; charset=utf-8', 'utf-8'],
			'tab ows' => ["text/html\t;charset=utf-8", 'utf-8'],
			'quoted value decoded' => ['text/html; charset="utf-8"', 'utf-8'],
			'value lowercased (charset is case-insensitive)' => ['text/html; charset=UTF-8', 'utf-8'],
			'name case-insensitive' => ['text/html; CharSet=utf-8', 'utf-8'],
			'first duplicate wins' => ['text/html; charset=utf-8; charset=ascii', 'utf-8'],
			'after quoted semicolon' => ['text/html; title="a;b"; charset=utf-8', 'utf-8'],
			'after empty parameter slots' => ['text/html;;charset=utf-8', 'utf-8'],
			'quoted uppercase decoded then lowercased' => ['text/html; charset="UTF-8"', 'utf-8'],
		];
	}

	public function testParsesMultipleParameters(): void
	{
		$mediaType = $this->factory->create('text/plain; charset=utf-8; format=flowed');

		$this->assertCount(2, $mediaType->parameters());
		$this->assertSame('flowed', (string) $mediaType->parameter('format')->value());
	}

	/**
	 * "+" and "." are tchars, so structured-syntax suffixes parse intact
	 */
	public function testStructuredSyntaxSuffix(): void
	{
		$mediaType = $this->factory->create('application/vnd.api+json');

		$this->assertSame('application', $mediaType->type());
		$this->assertSame('vnd.api+json', $mediaType->subtype());
	}

	public function testTrailingSeparatorIsLegal(): void
	{
		$this->assertSame('html', $this->factory->create('text/html;')->subtype());
	}

	public function testTrimsSurroundingOws(): void
	{
		$mediaType = $this->factory->create(" \ttext/html\t ");

		$this->assertSame('text', $mediaType->type());
		$this->assertSame('html', $mediaType->subtype());
	}

	/**
	 * "*" is a tchar, so a wildcard parses at the grammar level;
	 * range semantics belong to content negotiation, not here
	 */
	public function testWildcardIsGrammaticallyValid(): void
	{
		$mediaType = $this->factory->create('*/*');

		$this->assertSame('*', $mediaType->type());
		$this->assertSame('*', $mediaType->subtype());
	}

	public function testTryCreateNullIsNull(): void
	{
		$this->assertNull($this->factory->tryCreate(null));
	}

	public function testTryCreateDelegates(): void
	{
		$this->assertSame('json', $this->factory->tryCreate('application/json')->subtype());
	}

	#[DataProvider('invalidEssenceProvider')]
	public function testRejectsInvalidEssence(string $wire): void
	{
		$this->expectException(TokenException::class);

		$this->factory->create($wire);
	}

	public static function invalidEssenceProvider(): array
	{
		return [
			'empty' => [''],
			'ows only' => [' '],
			'no slash' => ['texthtml'],
			'empty subtype' => ['text/'],
			'empty type' => ['/html'],
			'slash only' => ['/'],
			'second slash lands in subtype' => ['a/b/c'],
			'double slash' => ['text//html'],
			'space before slash' => ['text /html'],
			'space after slash' => ['text/ html'],
			'space inside subtype' => ['text/ht ml'],
			'only parameters' => ['; charset=utf-8'],
			'line feed is not ows' => ["\ntext/html"],
			'carriage return is not ows' => ["text/html\r"],
			'vertical tab is not ows' => ["\x0Btext/html"],
			'null byte is not ows' => ["\x00text/html"],
			'whitespace around parameter equals' => ['text/html; charset = utf-8'],
		];
	}

	public function testRejectsValuelessParameter(): void
	{
		$this->expectException(TokenException::class);

		$this->factory->create('text/html; charset');
	}

	public function testRejectsDamagedQuotedParameterValue(): void
	{
		$this->expectException(QuotedStringFactoryException::class);

		$this->factory->create('text/html; charset="utf-8');
	}
}
