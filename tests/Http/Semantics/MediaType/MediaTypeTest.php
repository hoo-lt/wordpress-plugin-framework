<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaType::class)]
final class MediaTypeTest extends TestCase
{
	public function testAccessors(): void
	{
		$parameters = new Parameters([new Parameter('charset', 'utf-8')]);
		$mediaType = new MediaType('text', 'html', $parameters);

		$this->assertSame('text', $mediaType->type());
		$this->assertSame('html', $mediaType->subtype());
		$this->assertSame($parameters, $mediaType->parameters());
	}

	public function testParameterDelegatesToParameters(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$mediaType = new MediaType('text', 'html', new Parameters([$charset]));

		$this->assertSame($charset, $mediaType->parameter('charset'));
		$this->assertSame($charset, $mediaType->parameter('CHARSET'));
		$this->assertNull($mediaType->parameter('boundary'));
	}
}
