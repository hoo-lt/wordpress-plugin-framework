<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaTypeFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §8.3.1 — media-type = type "/" subtype parameters
 *                   type = token ; subtype = token
 *          §5.6.6 — parameters = *( OWS ";" OWS [ parameter ] )
 *
 * type and subtype are case-insensitive (folded to lower case). A media-type has no
 * weight — a "q" here is an ordinary media-type parameter (weight belongs to Accept, §12.5.1).
 *
 * The scan owns the parameters framing:
 *  - the OWS around ";" is the grammar's own — but NO whitespace around "=" ("not even bad whitespace");
 *  - OWS is SP / HTAB only (§5.6.3) — nothing else is whitespace;
 *  - a quoted value is consumed whole: ";" and "," inside a quoted-string are data, not framing;
 *  - empty and malformed elements are dropped.
 */
#[CoversClass(MediaTypeFactory::class)]
#[CoversClass(MediaType::class)]
final class MediaTypeFactoryTest extends TestCase
{
	private MediaTypeFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new MediaTypeFactory(new ParameterFactory());
	}

	#[DataProvider('mediaTypeProvider')]
	public function testCreate(string $wire, string $type, string $subtype, array $pairs): void
	{
		$mediaType = $this->factory->create($wire);

		$this->assertSame($type, $mediaType->type());
		$this->assertSame($subtype, $mediaType->subtype());
		$this->assertSame($pairs, self::pairs($mediaType->parameters()));
	}

	public static function mediaTypeProvider(): array
	{
		return [
			// --- essence -------------------------------------------------------------
			'bare essence'                     => ['text/html', 'text', 'html', []],
			'type and subtype lowercased'      => ['TEXT/HTML', 'text', 'html', []],
			'structured suffix subtype'        => ['application/vnd.api+json', 'application', 'vnd.api+json', []],

			// --- parameters ------------------------------------------------------------
			'single parameter'                 => ['text/html;charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'multiple parameters keep order'   => ['text/html;charset=utf-8;boundary=xyz', 'text', 'html', [['charset', 'utf-8'], ['boundary', 'xyz']]],
			'quoted parameter'                 => ['multipart/form-data;boundary="a,b"', 'multipart', 'form-data', [['boundary', 'a,b']]],
			'duplicate names are both kept'    => ['text/html;a=1;a=2', 'text', 'html', [['a', '1'], ['a', '2']]],

			// a media-type has no weight: "q" is just a parameter here — the §12.5.1 recipient
			// rule ("process any parameter named q as weight") is Accept-specific
			'q is an ordinary parameter'       => ['text/html;q=0.5', 'text', 'html', [['q', '0.5']]],

			// --- OWS framing (§5.6.6: the OWS around ";" is the grammar's own) ---------
			'OWS after semicolon (SP)'         => ['text/html; charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'OWS after semicolon (HTAB)'       => ["text/html;\tcharset=utf-8", 'text', 'html', [['charset', 'utf-8']]],
			'OWS before semicolon'             => ['text/html ;charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'OWS on both sides of semicolon'   => ['text/html ; charset=utf-8 ; boundary=x', 'text', 'html', [['charset', 'utf-8'], ['boundary', 'x']]],

			// --- NO whitespace around "=" (RFC 9110 §5.6.6, tightened from RFC 7231 BWS) ---
			'space before equals is rejected'  => ['text/html;charset =utf-8', 'text', 'html', []],
			'space after equals is rejected'   => ['text/html;charset= utf-8', 'text', 'html', []],
			'space before quoted is rejected'  => ['text/html;title= "v"', 'text', 'html', []],

			// --- OWS is SP / HTAB only (RFC 9110 §5.6.3) — nothing else is whitespace ---
			'LF is not OWS'                    => ["text/html;\ncharset=utf-8", 'text', 'html', []],
			'VT is not OWS'                    => ["text/html;\x0Bcharset=utf-8", 'text', 'html', []],

			// --- quoted-string content is inviolable: the scan can never resume inside it ---
			'quoted holds comma and semicolon' => ['text/html;title="a;b,c"', 'text', 'html', [['title', 'a;b,c']]],
			'parameter follows quoted comma'   => ['text/html;a="x,y";b=2', 'text', 'html', [['a', 'x,y'], ['b', '2']]],

			// --- malformed / degenerate elements dropped -------------------------------
			'valueless parameter is ignored'   => ['text/html;flag', 'text', 'html', []],
			'valueless then valid parameter'   => ['text/html;flag;charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'empty token value is ignored'     => ['text/html;charset=', 'text', 'html', []],
			'missing name is ignored'          => ['text/html;=utf-8', 'text', 'html', []],
			'unterminated quote is ignored'    => ['text/html;title="abc', 'text', 'html', []],
			'bare semicolon is skipped'        => ['text/html;;charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'junk after token value dropped'   => ['text/html;a=b"c', 'text', 'html', [['a', 'b']]],   // token ends at DQUOTE; the dangling quote is garbage, not framing

			// recipient robustness: essence is a prefix; junk after it carries no parameters
			'trailing junk keeps essence'      => ['text/html garbage', 'text', 'html', []],

			// malformed essence → empty-but-present type/subtype; parameters still scanned
			'no slash'                         => ['text', '', '', []],
			'missing subtype'                  => ['text/', '', '', []],
			'missing type'                     => ['/html', '', '', []],
			'empty string'                     => ['', '', '', []],
			'parameters without essence'       => [';charset=utf-8', '', '', [['charset', 'utf-8']]],
		];
	}

	public function testTryCreateNullIsNull(): void
	{
		$this->assertNull($this->factory->tryCreate(null));
	}

	public function testTryCreateStringDelegatesToCreate(): void
	{
		$mediaType = $this->factory->tryCreate('text/html');

		$this->assertNotNull($mediaType);
		$this->assertSame('text', $mediaType->type());
		$this->assertSame('html', $mediaType->subtype());
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	private static function pairs(array $parameters): array
	{
		$pairs = [];
		foreach ($parameters as $parameter) {
			$pairs[] = [$parameter->name(), $parameter->value()];
		}

		return $pairs;
	}
}
