<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\Parameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaType::class)]
final class MediaTypeTest extends TestCase
{
	public function testAccessors(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$mediaType = new MediaType('text', 'html', [$charset]);

		$this->assertSame('text', $mediaType->type());
		$this->assertSame('html', $mediaType->subtype());
		$this->assertSame([$charset], $mediaType->parameters());
	}

	public function testParameterLookupIsCaseInsensitive(): void
	{
		$charset = new Parameter('charset', 'utf-8');
		$mediaType = new MediaType('text', 'html', [$charset, new Parameter('boundary', 'xyz')]);

		// stored names are already folded; the lookup folds the query too (RFC 9110 §5.6.6)
		$this->assertSame($charset, $mediaType->parameter('charset'));
		$this->assertSame($charset, $mediaType->parameter('CHARSET'));
		$this->assertSame($charset, $mediaType->parameter('ChArSeT'));
	}

	public function testMissingParameterIsNull(): void
	{
		$mediaType = new MediaType('text', 'html', [new Parameter('charset', 'utf-8')]);

		$this->assertNull($mediaType->parameter('boundary'));
	}

	public function testDuplicateNameReturnsFirst(): void
	{
		$first = new Parameter('a', '1');
		$mediaType = new MediaType('text', 'html', [$first, new Parameter('a', '2')]);

		$this->assertSame($first, $mediaType->parameter('a'));
	}

	public function testEmptyParameters(): void
	{
		$mediaType = new MediaType('text', 'html', []);

		$this->assertSame([], $mediaType->parameters());
		$this->assertNull($mediaType->parameter('anything'));
	}
}
