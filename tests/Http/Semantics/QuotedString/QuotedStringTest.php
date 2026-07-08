<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\QuotedString;

use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedString;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * the VO holds the decoded value; the octets a quoted-string can carry after
 * unescaping are HTAB / SP / VCHAR / obs-text per RFC 9110 section 5.6.4
 */
#[CoversClass(QuotedString::class)]
final class QuotedStringTest extends TestCase
{
	#[DataProvider('validProvider')]
	public function testAcceptsDecodedValue(string $value): void
	{
		$this->assertSame($value, (string) new QuotedString($value));
	}

	public static function validProvider(): array
	{
		return [
			'empty' => [''],
			'plain' => ['utf-8'],
			'space' => ['a b'],
			'horizontal tab' => ["a\tb"],
			'double quote (decoded quoted-pair)' => ['a"b'],
			'backslash (decoded quoted-pair)' => ['a\\b'],
			'vchar bounds' => ["\x21\x7E"],
			'obs-text bounds' => ["\x80\xFF"],
		];
	}

	#[DataProvider('invalidProvider')]
	public function testRejectsUncarriableOctets(string $value): void
	{
		$this->expectException(QuotedStringException::class);

		new QuotedString($value);
	}

	public static function invalidProvider(): array
	{
		return [
			'null byte' => ["\x00"],
			'bell' => ["\x07"],
			'backspace (boundary below HTAB)' => ["\x08"],
			'line feed' => ["a\nb"],
			'vertical tab' => ["\x0B"],
			'carriage return' => ["a\rb"],
			'unit separator' => ["\x1F"],
			'delete' => ["\x7F"],
		];
	}
}
