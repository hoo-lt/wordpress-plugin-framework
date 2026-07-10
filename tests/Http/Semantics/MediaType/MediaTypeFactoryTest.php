<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaType;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaType\MediaTypeFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersFactory;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RFC 9110 §8.3.1 — media-type = type "/" subtype parameters
 *                   type = token ; subtype = token
 *
 * type and subtype are case-insensitive (folded to lower case). A media-type has no
 * weight — a "q" here is an ordinary media-type parameter (weight belongs to Accept, §12.5.1).
 */
#[CoversClass(MediaTypeFactory::class)]
#[CoversClass(MediaType::class)]
final class MediaTypeFactoryTest extends TestCase
{
	private MediaTypeFactory $factory;

	protected function setUp(): void
	{
		$this->factory = new MediaTypeFactory(new ParametersFactory());
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
			'bare essence'                  => ['text/html', 'text', 'html', []],
			'type and subtype lowercased'   => ['TEXT/HTML', 'text', 'html', []],
			'structured suffix subtype'     => ['application/vnd.api+json', 'application', 'vnd.api+json', []],
			'single parameter'              => ['text/html;charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'parameter with OWS framing'    => ['text/html; charset=utf-8', 'text', 'html', [['charset', 'utf-8']]],
			'quoted parameter'              => ['multipart/form-data;boundary="a,b"', 'multipart', 'form-data', [['boundary', 'a,b']]],

			// a media-type has no weight: "q" is just a parameter here — the §12.5.1 recipient
			// rule ("process any parameter named q as weight") is Accept-specific
			'q is an ordinary parameter'    => ['text/html;q=0.5', 'text', 'html', [['q', '0.5']]],

			// recipient robustness: essence is a prefix; junk after it carries no parameters
			'trailing junk keeps essence'   => ['text/html garbage', 'text', 'html', []],

			// malformed essence → empty-but-present type/subtype
			'no slash'                      => ['text', '', '', []],
			'missing subtype'               => ['text/', '', '', []],
			'missing type'                  => ['/html', '', '', []],
			'empty string'                  => ['', '', '', []],
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
	private static function pairs(ParametersInterface $parameters): array
	{
		$pairs = [];
		foreach ($parameters as $parameter) {
			$pairs[] = [$parameter->name(), $parameter->value()];
		}

		return $pairs;
	}
}
