<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\QuotedString;

use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedString;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringFactoryException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * the factory takes the wire format:
 * quoted-string = DQUOTE *( qdtext / quoted-pair ) DQUOTE per RFC 9110 section 5.6.4
 */
#[CoversClass(QuotedStringFactory::class)]
#[CoversClass(QuotedString::class)]
final class QuotedStringFactoryTest extends TestCase
{
	private QuotedStringFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new QuotedStringFactory();
	}

	#[DataProvider('wireProvider')]
	public function testDecodesWireFormat(string $wire, string $decoded): void
	{
		$this->assertSame($decoded, (string) $this->factory->create($wire));
	}

	public static function wireProvider(): array
	{
		return [
			'empty' => ['""', ''],
			'plain' => ['"utf-8"', 'utf-8'],
			'space and tab are qdtext' => ["\"a \tb\"", "a \tb"],
			'escaped quote' => ['"a\\"b"', 'a"b'],
			'escaped backslash' => ['"a\\\\b"', 'a\\b'],
			'escaped backslash then qdtext quote-candidate' => ['"\\\\\\""', '\\"'],
			'quoted-pair decoded even when unnecessary' => ['"\\a"', 'a'],
			'escaped tab' => ["\"\\\t\"", "\t"],
			'escaped space' => ['"\\ "', ' '],
			'escaped obs-text low bound' => ["\"\\\x80\"", "\x80"],
			'escaped obs-text high bound' => ["\"\\\xFF\"", "\xFF"],
			'quoted-pair immediately before closing quote' => ['"a\\\\"', 'a\\'],
			'escaped vchar bounds' => ["\"\\\x21\\\x7E\"", "\x21\x7E"],
			'qdtext boundaries' => ["\"\x21\x23\x5B\x5D\x7E\"", "\x21\x23\x5B\x5D\x7E"],
			'obs-text qdtext bounds' => ["\"\x80\xFF\"", "\x80\xFF"],
		];
	}

	#[DataProvider('invalidWireProvider')]
	public function testRejectsInvalidWireFormat(string $wire): void
	{
		$this->expectException(QuotedStringFactoryException::class);

		$this->factory->create($wire);
	}

	public static function invalidWireProvider(): array
	{
		return [
			'empty string' => [''],
			'unquoted' => ['utf-8'],
			'missing closing quote' => ['"abc'],
			'missing opening quote' => ['abc"'],
			'lone quote' => ['"'],
			'unescaped quote inside' => ['"a"b"'],
			'content after closing quote' => ['"a"b'],
			'content before opening quote' => ['a"b"'],
			'trailing backslash escapes closing quote' => ['"a\\"'],
			'backslash before line feed (not escapable)' => ["\"a\\\nb\""],
			'backslash before delete (not escapable)' => ["\"\\\x7F\""],
			'backslash before backspace (not escapable)' => ["\"\\\x08\""],
			'backslash before null (not escapable)' => ["\"\\\x00\""],
			'unescaped backspace (boundary below HTAB)' => ["\"a\x08b\""],
			'unescaped unit separator (boundary below SP)' => ["\"a\x1Fb\""],
			'unescaped line feed' => ["\"a\nb\""],
			'unescaped carriage return' => ["\"a\rb\""],
			'null byte' => ["\"a\x00b\""],
			'vertical tab' => ["\"a\x0Bb\""],
			'unescaped delete' => ["\"a\x7Fb\""],
		];
	}
}
