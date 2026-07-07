<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Parameter;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterInterface;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameters;
use Hoo\WordPressPluginFramework\Http\Semantics\QuotedString\QuotedString;
use Hoo\WordPressPluginFramework\Http\Semantics\Token\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaType::class)]
final class MediaTypeTest extends TestCase
{
	public function testExposesTypeAndSubtype(): void
	{
		$mediaType = $this->mediaType();

		$this->assertSame('text', $mediaType->type());
		$this->assertSame('html', $mediaType->subtype());
	}

	public function testExposesParameters(): void
	{
		$level = new Parameter(new Token('level'), new Token('1'));

		$mediaType = $this->mediaType($level);

		$this->assertCount(1, $mediaType->parameters());
		$this->assertSame($level, $mediaType->parameter('level'));
		$this->assertNull($mediaType->parameter('charset'));
	}

	public function testCharsetAbsentIsNull(): void
	{
		$this->assertNull($this->mediaType()->charset());
	}

	/**
	 * charset names are case-insensitive, so the accessor normalizes to lowercase
	 */
	public function testCharsetFromTokenValueIsLowercased(): void
	{
		$charset = new Parameter(new Token('charset'), new Token('UTF-8'));

		$this->assertSame('utf-8', $this->mediaType($charset)->charset());
	}

	public function testCharsetFromQuotedStringValue(): void
	{
		$charset = new Parameter(new Token('charset'), new QuotedString('utf-8'));

		$this->assertSame('utf-8', $this->mediaType($charset)->charset());
	}

	/**
	 * null means no charset parameter; '' means charset="" was present
	 */
	public function testEmptyCharsetIsPresentButEmpty(): void
	{
		$charset = new Parameter(new Token('charset'), new QuotedString(''));

		$this->assertSame('', $this->mediaType($charset)->charset());
	}

	private function mediaType(ParameterInterface ...$parameters): MediaType
	{
		return new MediaType(
			new Token('text'),
			new Token('html'),
			new Parameters($parameters),
		);
	}
}
