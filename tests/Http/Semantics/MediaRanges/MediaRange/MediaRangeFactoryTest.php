<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRange;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRangeFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §12.5.1 — media-range = ( "*" "/" "*" | type "/" "*" | type "/" subtype ) parameters
 *          §12.4.2 — qvalue = ( "0" [ "." 0*3DIGIT ] ) / ( "1" [ "." 0*3("0") ] ) ; "q" is case-insensitive
 *
 * Recipient rules exercised here:
 *  - "Recipients SHOULD process any parameter named "q" as weight, regardless of parameter
 *    ordering." (§12.5.1) — a valid weight is never a media-range parameter (the media type
 *    registry disallows parameters named "q"); with duplicates, the first valid q wins — the
 *    same rule MediaRange::parameter() applies to any duplicate name;
 *  - the weight grammar is bare ("q=" qvalue): a q whose value is quoted, out of range, or
 *    over-precise is no weight at all — it stays on the wire as a visible parameter;
 *  - weight absent on the wire decodes to 1.0 (§12.4.2: "If no 'q' parameter is present, the
 *    default weight is 1") — the VO holds the decoded weight, and only qvalue exists on the wire;
 *  - no whitespace around "=" — "not even bad whitespace" (§5.6.6 Note): such a token is
 *    neither weight nor parameter;
 *  - quoted-string content is inviolable: a "q=..." inside quotes is data.
 */
#[CoversClass(MediaRangeFactory::class)]
#[CoversClass(MediaRange::class)]
final class MediaRangeFactoryTest extends TestCase
{
	private MediaRangeFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new MediaRangeFactory(new ParametersFactory());
	}

	#[DataProvider('mediaRangeProvider')]
	public function testCreate(string $wire, string $type, string $subtype, float $weight, array $pairs): void
	{
		$range = $this->factory->create($wire);

		$this->assertSame($type, $range->type());
		$this->assertSame($subtype, $range->subtype());
		$this->assertSame($weight, $range->weight());
		$this->assertSame($pairs, self::pairs($range->parameters()));
	}

	public static function mediaRangeProvider(): array
	{
		return [
			// --- essence ------------------------------------------------------------
			'bare essence'                   => ['text/html', 'text', 'html', 1.0, []],
			'wildcard */*'                   => ['*/*', '*', '*', 1.0, []],
			'type with wildcard subtype'     => ['text/*', 'text', '*', 1.0, []],
			'essence lowercased'             => ['TEXT/HTML', 'text', 'html', 1.0, []],
			'essence and params lowercased'  => ['TEXT/HTML;CHARSET=UTF-8', 'text', 'html', 1.0, [['charset', 'UTF-8']]],
			'structured suffix subtype'      => ['application/vnd.api+json', 'application', 'vnd.api+json', 1.0, []],
			'star type, concrete subtype'    => ['*/html', '*', 'html', 1.0, []],   // "*" is a token char: grammar admits it; ranking it is negotiation's concern

			// --- weight present (§12.4.2) ------------------------------------------
			'weight only'                    => ['text/html;q=0.5', 'text', 'html', 0.5, []],
			'weight q=1'                     => ['text/html;q=1', 'text', 'html', 1.0, []],
			'weight q=1.000'                 => ['text/html;q=1.000', 'text', 'html', 1.0, []],
			'weight q=0'                     => ['text/html;q=0', 'text', 'html', 0.0, []],
			'weight q=0.000'                 => ['text/html;q=0.000', 'text', 'html', 0.0, []],   // explicit zero is present, not absent
			'weight q=0.999'                 => ['text/html;q=0.999', 'text', 'html', 0.999, []],
			'weight uppercase Q'             => ['text/html;Q=0.5', 'text', 'html', 0.5, []],
			'weight trailing-dot zero'       => ['text/html;q=0.', 'text', 'html', 0.0, []],
			'weight trailing-dot one'        => ['text/html;q=1.', 'text', 'html', 1.0, []],
			'weight on wildcard zero'        => ['*/*;q=0', '*', '*', 0.0, []],

			// --- OWS framing around the weight's ";" --------------------------------
			'weight OWS before semicolon'    => ['text/html ;q=0.5', 'text', 'html', 0.5, []],
			'weight OWS after semicolon'     => ['text/html; q=0.5', 'text', 'html', 0.5, []],
			'weight SP both sides'           => ['text/html ; q=0.5', 'text', 'html', 0.5, []],
			'weight HTAB both sides'         => ["text/html\t;\tq=0.5", 'text', 'html', 0.5, []],

			// --- weight absent → decoded default 1.0 (§12.4.2) ---------------------
			'no weight, one parameter'       => ['text/html;charset=utf-8', 'text', 'html', 1.0, [['charset', 'utf-8']]],

			// --- q is the weight regardless of ordering (§12.5.1 SHOULD) -----------
			'parameters then weight'         => ['text/html;charset=utf-8;q=0.8', 'text', 'html', 0.8, [['charset', 'utf-8']]],
			'weight before parameters'       => ['text/html;q=0.5;charset=utf-8', 'text', 'html', 0.5, [['charset', 'utf-8']]],
			'weight between parameters'      => ['text/html;a=1;q=0.5;b=2', 'text', 'html', 0.5, [['a', '1'], ['b', '2']]],
			'params then uppercase Q'        => ['text/html;charset=utf-8;Q=0.8', 'text', 'html', 0.8, [['charset', 'utf-8']]],
			'first q wins'                   => ['text/html;q=0.5;q=0.8', 'text', 'html', 0.5, []],
			'first valid q wins'             => ['text/html;q=junk;q=0.5', 'text', 'html', 0.5, [['q', 'junk']]],
			'later invalid q is a parameter' => ['text/html;q=0.8;q=junk', 'text', 'html', 0.8, [['q', 'junk']]],
			'weight without essence'         => [';q=0.5', '', '', 0.5, []],

			// --- outside the qvalue grammar: no weight, stays a visible parameter --
			'qvalue above one'               => ['text/html;q=1.5', 'text', 'html', 1.0, [['q', '1.5']]],
			'qvalue two'                     => ['text/html;q=2', 'text', 'html', 1.0, [['q', '2']]],
			'qvalue over one, zeros rule'    => ['text/html;q=1.001', 'text', 'html', 1.0, [['q', '1.001']]],
			'qvalue one, four zeros'         => ['text/html;q=1.0000', 'text', 'html', 1.0, [['q', '1.0000']]],
			'qvalue over-precise'            => ['text/html;q=0.1234', 'text', 'html', 1.0, [['q', '0.1234']]],
			'qvalue double zero'             => ['text/html;q=00.5', 'text', 'html', 1.0, [['q', '00.5']]],
			'qvalue padded integer'          => ['text/html;q=01', 'text', 'html', 1.0, [['q', '01']]],
			'qvalue no integer part'         => ['text/html;q=.5', 'text', 'html', 1.0, [['q', '.5']]],
			'qvalue negative'                => ['text/html;q=-0.5', 'text', 'html', 1.0, [['q', '-0.5']]],
			'qvalue trailing junk'           => ['text/html;q=0.5x', 'text', 'html', 1.0, [['q', '0.5x']]],
			'quoted qvalue is not a weight'  => ['text/html;q="0.5"', 'text', 'html', 1.0, [['q', '0.5']]],   // weight grammar is bare ("q=" qvalue, §12.4.2)

			// --- no whitespace around "=" (§5.6.6 Note) — not weight, not parameter -
			'space after equals'             => ['text/html;q= 0.5', 'text', 'html', 1.0, []],
			'space before equals'            => ['text/html;q =0.5', 'text', 'html', 1.0, []],

			// --- "q" inside a quoted value is data, not the weight ------------------
			'quoted q text is data'          => ['text/html;title="q=1";q=0.5', 'text', 'html', 0.5, [['title', 'q=1']]],
			'quoted q before a delimiter'    => ['text/html;title="a;q=0.9;x"', 'text', 'html', 1.0, [['title', 'a;q=0.9;x']]],   // a ";" right after the quoted qvalue must not fake a boundary
			'escaped quotes around fake q'   => ['text/html;title="a\";q=0.7\"b";q=0.2', 'text', 'html', 0.2, [['title', 'a";q=0.7"b']]],

			// --- degenerate / robustness --------------------------------------------
			'no slash'                       => ['not-a-range', '', '', 1.0, []],
			'empty string'                   => ['', '', '', 1.0, []],
			'trailing junk is ignored'       => ['text/html extra', 'text', 'html', 1.0, []],
			'valueless parameter dropped'    => ['text/html;flag', 'text', 'html', 1.0, []],
		];
	}

	public function testTryCreateNullIsNull(): void
	{
		$this->assertNull($this->factory->tryCreate(null));
	}

	public function testTryCreateStringDelegatesToCreate(): void
	{
		$range = $this->factory->tryCreate('text/html;q=0.5');

		$this->assertNotNull($range);
		$this->assertSame('text', $range->type());
		$this->assertSame(0.5, $range->weight());
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
