<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRange;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaRange::class)]
final class MediaRangeTest extends TestCase
{
	public function testAccessorsWithWeight(): void
	{
		$parameters = new Parameters([new Parameter('charset', 'utf-8')]);
		$range = new MediaRange('text', 'html', $parameters, 0.5);

		$this->assertSame('text', $range->type());
		$this->assertSame('html', $range->subtype());
		$this->assertSame($parameters, $range->parameters());
		$this->assertSame(0.5, $range->weight());
	}

	public function testWeightDefaultsToNullAbsence(): void
	{
		$range = new MediaRange('text', 'html', new Parameters([]));

		$this->assertNull($range->weight());
	}

	public function testExplicitZeroWeightIsNotAbsence(): void
	{
		$range = new MediaRange('text', 'html', new Parameters([]), 0.0);

		$this->assertSame(0.0, $range->weight());
	}

	public function testParameterDelegatesToParameters(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$range = new MediaRange('text', 'html', new Parameters([$charset]));

		$this->assertSame($charset, $range->parameter('charset'));
		$this->assertSame($charset, $range->parameter('CHARSET'));
		$this->assertNull($range->parameter('boundary'));
	}
}
