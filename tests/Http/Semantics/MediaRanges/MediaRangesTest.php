<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaRanges;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRange;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRanges;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaRanges::class)]
final class MediaRangesTest extends TestCase
{
	public function testCountAndIterationPreserveOrder(): void
	{
		$html = new MediaRange('text', 'html', new Parameters([]));
		$json = new MediaRange('application', 'json', new Parameters([]));
		$ranges = new MediaRanges([$html, $json]);

		$this->assertCount(2, $ranges);
		$this->assertSame([$html, $json], iterator_to_array($ranges));
	}

	public function testEmpty(): void
	{
		$ranges = new MediaRanges([]);

		$this->assertCount(0, $ranges);
		$this->assertSame([], iterator_to_array($ranges));
	}
}
