<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRange;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\Parameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaRange::class)]
final class MediaRangeTest extends TestCase
{
	public function testAccessorsWithWeight(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$range = new MediaRange('text', 'html', [$charset], 0.5);

		$this->assertSame('text', $range->type());
		$this->assertSame('html', $range->subtype());
		$this->assertSame([$charset], $range->parameters());
		$this->assertSame(0.5, $range->weight());
	}

	public function testWeightDefaultsToOne(): void
	{
		$range = new MediaRange('text', 'html', []);

		$this->assertSame(1.0, $range->weight());   // no q on the wire decodes to the §12.4.2 default of 1
	}

	public function testExplicitZeroWeightIsNotDefaulted(): void
	{
		$range = new MediaRange('text', 'html', [], 0.0);

		$this->assertSame(0.0, $range->weight());
	}

	public function testParameterLookupIsCaseInsensitive(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$range = new MediaRange('text', 'html', [$charset, new Parameter('boundary', 'xyz')]);

		// stored names are already folded; the lookup folds the query too (RFC 9110 §5.6.6)
		$this->assertSame($charset, $range->parameter('charset'));
		$this->assertSame($charset, $range->parameter('CHARSET'));
		$this->assertSame($charset, $range->parameter('ChArSeT'));
	}

	public function testMissingParameterIsNull(): void
	{
		$range = new MediaRange('text', 'html', [new Parameter('charset', 'utf-8')]);

		$this->assertNull($range->parameter('boundary'));
	}

	public function testDuplicateNameReturnsFirst(): void
	{
		$first = new Parameter('a', '1');
		$range = new MediaRange('text', 'html', [$first, new Parameter('a', '2')]);

		$this->assertSame($first, $range->parameter('a'));
	}

	public function testEmptyParameters(): void
	{
		$range = new MediaRange('text', 'html', []);

		$this->assertSame([], $range->parameters());
		$this->assertNull($range->parameter('anything'));
	}
}
