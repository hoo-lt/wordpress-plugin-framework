<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §5.6.6 — parameters = *( OWS ";" OWS parameter )
 *                   parameter  = parameter-name "=" parameter-value
 *                   parameter-value = ( token / quoted-string )
 *
 * Normative rules exercised here:
 *  - parameter names are case-insensitive (folded to lower case);
 *  - parameter values are preserved verbatim (case-significance is per-parameter semantics);
 *  - token and quoted-string forms are equivalent for the same value;
 *  - NO whitespace is permitted around "=" ("not even bad whitespace");
 *  - commas / semicolons inside a quoted-string are data, not framing.
 */
#[CoversClass(ParametersFactory::class)]
#[CoversClass(Parameters::class)]
#[CoversClass(Parameter::class)]
final class ParametersFactoryTest extends TestCase
{
	private ParametersFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new ParametersFactory();
	}

	#[DataProvider('parameterProvider')]
	public function testCreate(string $wire, array $expectedPairs): void
	{
		$this->assertSame($expectedPairs, self::pairs($this->factory->create($wire)));
	}

	public static function parameterProvider(): array
	{
		return [
			// --- presence / absence -------------------------------------------------
			'no parameters'                    => ['text/html', []],
			'single parameter'                 => ['text/html;charset=utf-8', [['charset', 'utf-8']]],
			'multiple parameters keep order'   => ['text/html;charset=utf-8;boundary=xyz', [['charset', 'utf-8'], ['boundary', 'xyz']]],

			// --- case handling (RFC 9110 §5.6.6) ------------------------------------
			'name is folded to lower case'     => ['text/html;CharSet=utf-8', [['charset', 'utf-8']]],
			'value case is preserved'          => ['text/html;charset=UTF-8', [['charset', 'UTF-8']]],

			// --- OWS framing --------------------------------------------------------
			'OWS after semicolon (SP)'         => ['text/html; charset=utf-8', [['charset', 'utf-8']]],
			'OWS after semicolon (HTAB)'       => ["text/html;\tcharset=utf-8", [['charset', 'utf-8']]],
			'OWS before semicolon is skipped'  => ['text/html ;charset=utf-8', [['charset', 'utf-8']]],
			'OWS on both sides of semicolon'   => ['text/html ; charset=utf-8 ; boundary=x', [['charset', 'utf-8'], ['boundary', 'x']]],

			// --- NO whitespace around "=" (RFC 9110 §5.6.6, tightened from RFC 7231 BWS) ---
			'space before equals is rejected'  => ['text/html;charset =utf-8', []],
			'space after equals is rejected'   => ['text/html;charset= utf-8', []],
			'space before quoted is rejected'  => ['text/html;title= "v"', []],

			// --- OWS is SP / HTAB only (RFC 9110 §5.6.3) — nothing else is whitespace ---
			'LF is not OWS'                    => ["text/html;\ncharset=utf-8", []],
			'VT is not OWS'                    => ["text/html;\x0Bcharset=utf-8", []],

			// --- quoted-string values -----------------------------------------------
			'quoted value is unquoted'         => ['text/html;title="hello world"', [['title', 'hello world']]],
			'quoted equals token value'        => ['text/html;charset="utf-8"', [['charset', 'utf-8']]],
			'empty quoted-string is empty'     => ['text/html;title=""', [['title', '']]],
			'quoted-pair escaped quote'        => ['text/html;title="a\"b"', [['title', 'a"b']]],
			'quoted-pair escaped backslash'    => ['text/html;title="a\\\\b"', [['title', 'a\\b']]],
			'quoted-pair escaped comma'        => ['text/html;title="a\,b"', [['title', 'a,b']]],
			'quoted-pair closes the value'     => ['text/html;title="ab\""', [['title', 'ab"']]],
			'quoted holds comma and semicolon' => ['text/html;title="a;b,c"', [['title', 'a;b,c']]],
			'HTAB inside quoted is qdtext'     => ["text/html;title=\"a\tb\"", [['title', "a\tb"]]],
			'obs-text inside quoted is kept'   => ["text/html;title=\"a\x80b\"", [['title', "a\x80b"]]],
			'parameter follows quoted comma'   => ['text/html;a="x,y";b=2', [['a', 'x,y'], ['b', '2']]],

			// --- malformed / degenerate elements ------------------------------------
			'valueless parameter is ignored'   => ['text/html;flag', []],
			'valueless then valid parameter'   => ['text/html;flag;charset=utf-8', [['charset', 'utf-8']]],
			'empty token value is ignored'     => ['text/html;charset=', []],
			'missing name is ignored'          => ['text/html;=utf-8', []],
			'unterminated quote is ignored'    => ['text/html;title="abc', []],
			'bare semicolon is skipped'        => ['text/html;;charset=utf-8', [['charset', 'utf-8']]],
			'duplicate names are both kept'    => ['text/html;a=1;a=2', [['a', '1'], ['a', '2']]],
			'token value special characters'   => ['text/html;ext=a.b+c-d!~', [['ext', 'a.b+c-d!~']]],
			'all-tchar name is folded'         => ["x;p!#$%&'*+-.^_`|~09AZaz=v", [["p!#$%&'*+-.^_`|~09azaz", 'v']]],
			'junk after token value dropped'   => ['text/html;a=b"c', [['a', 'b']]],   // token ends at DQUOTE; the dangling quote is garbage, not framing
		];
	}

	public function testCreateReturnsParametersInstance(): void
	{
		$this->assertInstanceOf(ParametersInterface::class, $this->factory->create('text/html'));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	private static function pairs(ParametersInterface $parameters): array
	{
		$pairs = [];
		foreach ($parameters as $parameter) {
			$pairs[] = [$parameter->name(), $parameter->value()];
		}

		return $pairs;
	}
}
