<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Coders\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaTypeException;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaTypeFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaTypeFactoryException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaType::class)]
#[CoversClass(MediaTypeFactory::class)]
final class MediaTypeTest extends TestCase
{
	private MediaTypeFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new MediaTypeFactory();
	}

	public function testNormalizesCase(): void
	{
		$mediaType = new MediaType('Application', 'JSON', 'UTF-8');

		$this->assertSame('application', $mediaType->type());
		$this->assertSame('json', $mediaType->subtype());
		$this->assertSame('utf-8', $mediaType->charset());
	}

	public function testToStringWithoutCharset(): void
	{
		$this->assertSame(
			'application/json',
			(string) new MediaType('application', 'json'),
		);
	}

	public function testToStringWithCharset(): void
	{
		$this->assertSame(
			'text/html; charset=utf-8',
			(string) new MediaType('text', 'html', 'utf-8'),
		);
	}

	#[DataProvider('invalidComponentProvider')]
	public function testRejectsInvalidComponents(string $type, string $subtype, ?string $charset): void
	{
		$this->expectException(MediaTypeException::class);

		new MediaType($type, $subtype, $charset);
	}

	public static function invalidComponentProvider(): array
	{
		return [
			'empty type' => ['', 'json', null],
			'wildcard type' => ['*', 'json', null],
			'empty subtype' => ['application', '', null],
			'wildcard subtype' => ['application', '*', null],
			'empty charset' => ['text', 'html', ''],
		];
	}

	public function testCreatesFromEssence(): void
	{
		$mediaType = $this->factory->create('application/json');

		$this->assertSame('application', $mediaType->type());
		$this->assertSame('json', $mediaType->subtype());
		$this->assertNull($mediaType->charset());
	}

	public function testParsesCharset(): void
	{
		$mediaType = $this->factory->create('Text/HTML; CharSet=UTF-8; level=1');

		$this->assertSame('utf-8', $mediaType->charset());
		$this->assertSame('text/html; charset=utf-8', (string) $mediaType);
	}

	public function testUnquotesCharset(): void
	{
		$this->assertSame(
			'utf-8',
			$this->factory->create('application/json; charset="UTF-8"')->charset(),
		);
	}

	#[DataProvider('missingCharsetProvider')]
	public function testMissingCharsetIsNull(string $mediaType): void
	{
		$this->assertNull($this->factory->create($mediaType)->charset());
	}

	public static function missingCharsetProvider(): array
	{
		return [
			'no parameters' => ['text/html'],
			'other parameters' => ['text/html; level=1'],
			'valueless charset' => ['text/html; charset'],
			'empty charset' => ['text/html; charset=""'],
		];
	}

	public function testNormalizesWireWhitespace(): void
	{
		$this->assertSame(
			'text/html; charset=utf-8',
			(string) $this->factory->create(' text/html ; charset = utf-8 '),
		);
	}

	#[DataProvider('damagedProvider')]
	public function testRejectsDamagedMediaType(string $mediaType): void
	{
		$this->expectException(MediaTypeFactoryException::class);

		$this->factory->create($mediaType);
	}

	public static function damagedProvider(): array
	{
		return [
			'empty' => [''],
			'no slash' => ['texthtml'],
			'two slashes' => ['a/b/c'],
			'only parameters' => ['; charset=utf-8'],
		];
	}

	public function testTryCreateNullPassesThrough(): void
	{
		$this->assertNull($this->factory->tryCreate(null));
	}

	public function testTryCreateDelegates(): void
	{
		$this->assertSame(
			'application/json',
			(string) $this->factory->tryCreate('application/json'),
		);
	}
}
