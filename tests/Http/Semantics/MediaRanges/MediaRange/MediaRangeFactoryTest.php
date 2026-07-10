<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRange;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRangeFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §12.5.1 — media-range = ( "*" "/" "*" | type "/" "*" | type "/" subtype ) parameters
 *          §12.4.2 — qvalue = ( "0" [ "." 0*3DIGIT ] ) / ( "1" [ "." 0*3("0") ] ) ; "q" is case-insensitive
 *
 * Recipient rules exercised here:
 *  - "Recipients SHOULD process any parameter named "q" as weight, regardless of parameter
 *    ordering." (§12.5.1) — a "q" is never a media-range parameter (the media type registry
 *    disallows them); each occurrence (re)sets the weight slot;
 *  - a q parameter's value may arrive quoted: quoted and unquoted values are equivalent (§5.6.6);
 *  - a decoded value outside the qvalue grammar sets no weight;
 *  - weight absent on the wire → null, never a defaulted 1.0 (the q=1 default is negotiation's);
 *  - no whitespace around "=" — "not even bad whitespace" (§5.6.6 Note).
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
	public function testCreate(string $wire, string $type, string $subtype, ?float $weight, array $pairs): void
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
			'bare essence'                   => ['text/html', 'text', 'html', null, []],
			'wildcard */*'                   => ['*/*', '*', '*', null, []],
			'type with wildcard subtype'     => ['text/*', 'text', '*', null, []],
			'essence lowercased'             => ['TEXT/HTML', 'text', 'html', null, []],
			'essence and params lowercased'  => ['TEXT/HTML;CHARSET=UTF-8', 'text', 'html', null, [['charset', 'UTF-8']]],
			'structured suffix subtype'      => ['application/vnd.api+json', 'application', 'vnd.api+json', null, []],
			'star type, concrete subtype'    => ['*/html', '*', 'html', null, []],   // "*" is a token char: grammar admits it; ranking it is negotiation's concern

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
			'quoted qvalue is equivalent'    => ['text/html;q="0.5"', 'text', 'html', 0.5, []],   // token / quoted-string equivalence (§5.6.6)

			// --- OWS framing around the weight's ";" --------------------------------
			'weight OWS before semicolon'    => ['text/html ;q=0.5', 'text', 'html', 0.5, []],
			'weight OWS after semicolon'     => ['text/html; q=0.5', 'text', 'html', 0.5, []],
			'weight SP both sides'           => ['text/html ; q=0.5', 'text', 'html', 0.5, []],
			'weight HTAB both sides'         => ["text/html\t;\tq=0.5", 'text', 'html', 0.5, []],

			// --- weight absent → null (never defaulted) ----------------------------
			'no weight, one parameter'       => ['text/html;charset=utf-8', 'text', 'html', null, [['charset', 'utf-8']]],

			// --- q is the weight regardless of ordering (§12.5.1 SHOULD) -----------
			'parameters then weight'         => ['text/html;charset=utf-8;q=0.8', 'text', 'html', 0.8, [['charset', 'utf-8']]],
			'weight before parameters'       => ['text/html;q=0.5;charset=utf-8', 'text', 'html', 0.5, [['charset', 'utf-8']]],
			'weight between parameters'      => ['text/html;a=1;q=0.5;b=2', 'text', 'html', 0.5, [['a', '1'], ['b', '2']]],
			'params then uppercase Q'        => ['text/html;charset=utf-8;Q=0.8', 'text', 'html', 0.8, [['charset', 'utf-8']]],
			'last q wins'                    => ['text/html;q=0.5;q=0.8', 'text', 'html', 0.8, []],
			'later invalid q resets'         => ['text/html;q=0.8;q=junk', 'text', 'html', null, []],
			'weight without essence'         => [';q=0.5', '', '', 0.5, []],

			// --- invalid qvalue sets no weight; q is never a parameter -------------
			'qvalue above one'               => ['text/html;q=1.5', 'text', 'html', null, []],
			'qvalue two'                     => ['text/html;q=2', 'text', 'html', null, []],
			'qvalue over one, zeros rule'    => ['text/html;q=1.001', 'text', 'html', null, []],
			'qvalue one, four zeros'         => ['text/html;q=1.0000', 'text', 'html', null, []],
			'qvalue over-precise'            => ['text/html;q=0.1234', 'text', 'html', null, []],
			'qvalue double zero'             => ['text/html;q=00.5', 'text', 'html', null, []],
			'qvalue padded integer'          => ['text/html;q=01', 'text', 'html', null, []],
			'qvalue no integer part'         => ['text/html;q=.5', 'text', 'html', null, []],
			'qvalue negative'                => ['text/html;q=-0.5', 'text', 'html', null, []],
			'qvalue trailing junk'           => ['text/html;q=0.5x', 'text', 'html', null, []],

			// --- no whitespace around "=" (§5.6.6 Note) — not weight, not parameter -
			'space after equals'             => ['text/html;q= 0.5', 'text', 'html', null, []],
			'space before equals'            => ['text/html;q =0.5', 'text', 'html', null, []],

			// --- "q" inside a quoted value is data, not the weight ------------------
			'quoted q text is data'          => ['text/html;title="q=1";q=0.5', 'text', 'html', 0.5, [['title', 'q=1']]],

			// --- degenerate / robustness --------------------------------------------
			'no slash'                       => ['not-a-range', '', '', null, []],
			'empty string'                   => ['', '', '', null, []],
			'trailing junk is ignored'       => ['text/html extra', 'text', 'html', null, []],
			'valueless parameter dropped'    => ['text/html;flag', 'text', 'html', null, []],
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
