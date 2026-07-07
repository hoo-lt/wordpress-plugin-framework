<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Token;

use Hoo\WordPressPluginFramework\Http\Semantics\Token\Token;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\TokenException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Token::class)]
final class TokenTest extends TestCase
{
	/**
	 * tchar = "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." /
	 *         "^" / "_" / "`" / "|" / "~" / DIGIT / ALPHA per RFC 9110 section 5.6.2
	 */
	private const TCHAR = "!#$%&'*+-.^_`|~"
		. '0123456789'
		. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
		. 'abcdefghijklmnopqrstuvwxyz';

	#[DataProvider('validProvider')]
	public function testAcceptsValidToken(string $token): void
	{
		$this->assertSame($token, (string) new Token($token));
	}

	public static function validProvider(): array
	{
		return [
			'single letter' => ['a'],
			'single digit' => ['0'],
			'single special' => ['!'],
			'every tchar' => [self::TCHAR],
			'case preserved' => ['AbC'],
			'wildcard is a tchar' => ['*'],
			'typical token' => ['application'],
		];
	}

	#[DataProvider('invalidProvider')]
	public function testRejectsInvalidToken(string $token): void
	{
		$this->expectException(TokenException::class);

		new Token($token);
	}

	public static function invalidProvider(): array
	{
		$cases = [
			'empty (token = 1*tchar)' => [''],
			'inner space' => ['ab cd'],
			'leading space' => [' a'],
			'trailing space' => ['a '],
			'horizontal tab' => ["a\tb"],
			'carriage return' => ["a\rb"],
			'line feed' => ["a\nb"],
			'trailing line feed' => ["a\n"],
			'null byte' => ["a\x00b"],
			'delete' => ["a\x7Fb"],
			'obs-text low bound' => ["a\x80b"],
			'obs-text high bound' => ["a\xFFb"],
			'multibyte utf-8' => ['naïve'],
		];

		foreach (str_split('"(),/:;<=>?@[\\]{}') as $delimiter) {
			$cases['delimiter ' . $delimiter] = ['a' . $delimiter . 'b'];
		}

		return $cases;
	}
}
