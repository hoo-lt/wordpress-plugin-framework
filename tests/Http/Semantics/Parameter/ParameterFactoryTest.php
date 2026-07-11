<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\Parameter;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §5.6.6 — parameter = parameter-name "=" parameter-value
 *                   parameter-value = ( token / quoted-string )
 *
 * The wire is one bare parameter — its OWS ";" OWS framing is the field scan's concern.
 *
 * Normative rules exercised here:
 *  - parameter names are case-insensitive (folded to lower case);
 *  - parameter values are preserved verbatim (case-significance is per-parameter semantics);
 *  - token and quoted-string forms are equivalent for the same value;
 *  - a quoted-string is unquoted: surrounding DQUOTEs stripped, quoted-pairs unescaped.
 */
#[CoversClass(ParameterFactory::class)]
#[CoversClass(Parameter::class)]
final class ParameterFactoryTest extends TestCase
{
	private ParameterFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new ParameterFactory();
	}

	#[DataProvider('parameterProvider')]
	public function testCreate(string $wire, string $name, string $value): void
	{
		$parameter = $this->factory->create($wire);

		$this->assertSame($name, $parameter->name());
		$this->assertSame($value, $parameter->value());
	}

	public static function parameterProvider(): array
	{
		return [
			// --- token values ---------------------------------------------------------
			'token value'                      => ['charset=utf-8', 'charset', 'utf-8'],
			'token value special characters'   => ['ext=a.b+c-d!~', 'ext', 'a.b+c-d!~'],

			// --- case handling (RFC 9110 §5.6.6) ---------------------------------------
			'name is folded to lower case'     => ['CharSet=utf-8', 'charset', 'utf-8'],
			'value case is preserved'          => ['charset=UTF-8', 'charset', 'UTF-8'],
			'all-tchar name is folded'         => ["p!#$%&'*+-.^_`|~09AZaz=v", "p!#$%&'*+-.^_`|~09azaz", 'v'],

			// --- quoted-string values ---------------------------------------------------
			'quoted value is unquoted'         => ['title="hello world"', 'title', 'hello world'],
			'quoted equals token value'        => ['charset="utf-8"', 'charset', 'utf-8'],
			'empty quoted-string is empty'     => ['title=""', 'title', ''],
			'quoted-pair escaped quote'        => ['title="a\"b"', 'title', 'a"b'],
			'quoted-pair escaped backslash'    => ['title="a\\\\b"', 'title', 'a\\b'],
			'quoted-pair escaped comma'        => ['title="a\,b"', 'title', 'a,b'],
			'quoted-pair closes the value'     => ['title="ab\""', 'title', 'ab"'],
			'quoted holds comma and semicolon' => ['title="a;b,c"', 'title', 'a;b,c'],
			'HTAB inside quoted is qdtext'     => ["title=\"a\tb\"", 'title', "a\tb"],
			'obs-text inside quoted is kept'   => ["title=\"a\x80b\"", 'title', "a\x80b"],

			// --- malformed / degenerate wires → empty-but-present facets ---------------
			'valueless wire'                   => ['flag', '', ''],
			'empty token value'                => ['charset=', '', ''],
			'missing name'                     => ['=utf-8', '', ''],
			'unterminated quote'               => ['title="abc', '', ''],
			'empty wire'                       => ['', '', ''],
			'junk after token value dropped'   => ['a=b"c', 'a', 'b'],   // token ends at DQUOTE; the dangling quote is garbage, not framing
		];
	}

	public function testCreateReturnsParameterInstance(): void
	{
		$this->assertInstanceOf(ParameterInterface::class, $this->factory->create('charset=utf-8'));
	}
}
