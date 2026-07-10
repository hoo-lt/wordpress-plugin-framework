<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaRanges;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRangeFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRangeInterface;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRanges;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRangesFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §12.5.1 — Accept = #( media-range [ weight ] )
 *          §5.6.1  — a #rule element list is framed by  OWS "," OWS ; a sender MUST NOT emit
 *                    empty elements, and a recipient MAY ignore them.
 *
 * The splitter is responsible only for list framing: it consumes OWS and commas, drops empty
 * elements, and hands each exact (OWS-free) element to the MediaRange factory. A comma inside a
 * quoted-string is data, not a delimiter.
 */
#[CoversClass(MediaRangesFactory::class)]
#[CoversClass(MediaRanges::class)]
final class MediaRangesFactoryTest extends TestCase
{
	private MediaRangesFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new MediaRangesFactory(new MediaRangeFactory(new ParametersFactory()));
	}

	#[DataProvider('acceptProvider')]
	public function testCreate(string $accept, array $expected): void
	{
		$ranges = $this->factory->create($accept);

		$this->assertSame($expected, self::structure($ranges));
	}

	public static function acceptProvider(): array
	{
		return [
			// --- basic list framing -------------------------------------------------
			'single element'              => ['text/html', [['text', 'html', null, []]]],
			'two elements'                => ['text/html,application/json', [['text', 'html', null, []], ['application', 'json', null, []]]],
			'OWS after comma'             => ['text/html, application/json', [['text', 'html', null, []], ['application', 'json', null, []]]],
			'OWS around comma'            => ['text/html , application/json', [['text', 'html', null, []], ['application', 'json', null, []]]],
			'HTAB after comma'            => ["text/html,\tapplication/json", [['text', 'html', null, []], ['application', 'json', null, []]]],
			'leading and trailing OWS'    => [' text/html ', [['text', 'html', null, []]]],

			// --- empty elements dropped (§5.6.1) -----------------------------------
			'empty element in the middle' => ['text/html,,application/json', [['text', 'html', null, []], ['application', 'json', null, []]]],
			'leading comma'               => [',text/html', [['text', 'html', null, []]]],
			'trailing comma'              => ['text/html,', [['text', 'html', null, []]]],
			'only commas'                 => [',,', []],
			'blank commas with OWS'       => [' , , ', []],
			'empty string'                => ['', []],

			// --- weights per element (§12.4.2) -------------------------------------
			'weights per element'         => ['text/html;q=0.5,application/json;q=0.8', [['text', 'html', 0.5, []], ['application', 'json', 0.8, []]]],
			'trailing OWS before comma'   => ['text/*;q=0.5 , text/html', [['text', '*', 0.5, []], ['text', 'html', null, []]]],

			// --- quoted comma is data, not a delimiter -----------------------------
			'quoted comma inside element' => ['text/html;title="a,b",application/json', [['text', 'html', null, [['title', 'a,b']]], ['application', 'json', null, []]]],
			'escaped quote before comma'  => ['t/h;x="a\",b",u/v', [['t', 'h', null, [['x', 'a",b']]], ['u', 'v', null, []]]],
			'quoted end, then list OWS'   => ['t/h;x="a,b" ,u/v', [['t', 'h', null, [['x', 'a,b']]], ['u', 'v', null, []]]],

			// --- comma is always framing outside a quoted-string (§5.6.1) ----------
			'comma inside a bad qvalue'   => ['text/html;q=0,5', [['text', 'html', 0.0, []], ['', '', null, []]]],   // "5" is its own (garbage) element

			// --- garbage containment ------------------------------------------------
			'lone star is not a range'    => ['*', [['', '', null, []]]],                                            // Accept knows "*/*", never a bare "*"
			'LF is not list OWS'          => ["text/html,\napplication/json", [['text', 'html', null, []], ['', '', null, []]]],   // strict: SP/HTAB only; the LF poisons its element

			// --- realistic Accept, order preserved ---------------------------------
			'graded wildcards'            => ['*/*;q=0.1, text/*;q=0.5, text/html', [['*', '*', 0.1, []], ['text', '*', 0.5, []], ['text', 'html', null, []]]],

			// --- RFC 9110 §12.5.1's own elaborate example ---------------------------
			'§12.5.1 example'             => [
				'text/*;q=0.3, text/plain;q=0.7, text/plain;format=flowed, text/plain;format=fixed;q=0.4, */*;q=0.5',
				[
					['text', '*', 0.3, []],
					['text', 'plain', 0.7, []],
					['text', 'plain', null, [['format', 'flowed']]],
					['text', 'plain', 0.4, [['format', 'fixed']]],
					['*', '*', 0.5, []],
				],
			],
		];
	}

	public function testTryCreateNullIsNull(): void
	{
		$this->assertNull($this->factory->tryCreate(null));
	}

	public function testTryCreateStringDelegatesToCreate(): void
	{
		$ranges = $this->factory->tryCreate('text/html,application/json');

		$this->assertNotNull($ranges);
		$this->assertCount(2, $ranges);
	}

	public function testEmptyStringYieldsNoRanges(): void
	{
		// the splitter yields zero ranges for an empty field-value; supplying the "*/*"
		// default is a negotiation concern, not a parsing one.
		$this->assertCount(0, $this->factory->create(''));
	}

	/**
	 * @return list<array{0: string, 1: string, 2: ?float, 3: list<array{0: string, 1: string}>}>
	 */
	private static function structure(iterable $ranges): array
	{
		$structured = [];
		foreach ($ranges as $range) {
			$structured[] = self::describe($range);
		}

		return $structured;
	}

	/**
	 * @return array{0: string, 1: string, 2: ?float, 3: list<array{0: string, 1: string}>}
	 */
	private static function describe(MediaRangeInterface $range): array
	{
		return [$range->type(), $range->subtype(), $range->weight(), self::pairs($range->parameters())];
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
