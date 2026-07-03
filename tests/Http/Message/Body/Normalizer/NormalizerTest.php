<?php

namespace Hoo\WordPressPluginFramework\Tests\Http\Message\Body\Normalizer;

use DateTimeImmutable;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\BackedEnum\Normalizer as BackedEnumNormalizer;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\DateTime\Normalizer as DateTimeNormalizer;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\Normalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

enum NormalizerTestSuit: string
{
	case Hearts = 'H';
	case Spades = 'S';
}

final class NormalizerTestDto
{
	public function __construct(
		public int $id,
		public array $tags,
	) {
	}
}

#[CoversClass(Normalizer::class)]
#[CoversClass(DateTimeNormalizer::class)]
#[CoversClass(BackedEnumNormalizer::class)]
final class NormalizerTest extends TestCase
{
	private Normalizer $normalizer;

	protected function setUp(): void
	{
		// only leaf value-transforms; array/object traversal is the composite's own job
		$this->normalizer = new Normalizer([
			new DateTimeNormalizer(),
			new BackedEnumNormalizer(),
		]);
	}

	#[DataProvider('scalarProvider')]
	public function testScalarsPassThrough(mixed $value): void
	{
		$this->assertSame($value, $this->normalizer->normalize($value));
	}

	public static function scalarProvider(): array
	{
		return [
			'int'          => [5],
			'float'        => [3.14],
			'bool'         => [true],
			'string'       => ['x'],
			'null'         => [null],
			'zero'         => [0],
			'empty string' => [''],
		];
	}

	#[DataProvider('shapeProvider')]
	public function testNormalizedShape(mixed $value, string $expectedJson): void
	{
		$this->assertSame(
			$expectedJson,
			json_encode($this->normalizer->normalize($value), JSON_UNESCAPED_UNICODE)
		);
	}

	public static function shapeProvider(): array
	{
		$at = new DateTimeImmutable('2026-07-03T10:00:00+00:00');

		return [
			'list stays array'          => [['a', 'b'], '["a","b"]'],
			'assoc array stays object'  => [['a' => 1], '{"a":1}'],
			'empty array stays array'   => [[], '[]'],
			'object stays object'       => [(object) ['a' => 1], '{"a":1}'],
			'empty object stays object' => [new stdClass(), '{}'],
			'nested empty object'       => [(object) ['meta' => new stdClass()], '{"meta":{}}'],
			'datetime to ATOM'          => [$at, '"2026-07-03T10:00:00+00:00"'],
			'backed enum to value'      => [NormalizerTestSuit::Hearts, '"H"'],
			'datetime beats object'     => [(object) ['at' => $at], '{"at":"2026-07-03T10:00:00+00:00"}'],
			'enum inside array'         => [[NormalizerTestSuit::Hearts, NormalizerTestSuit::Spades], '["H","S"]'],
			'dto to object'             => [new NormalizerTestDto(7, ['x']), '{"id":7,"tags":["x"]}'],
			'deep mixed'                => [
				(object) ['rows' => [(object) ['at' => $at, 'tags' => []]]],
				'{"rows":[{"at":"2026-07-03T10:00:00+00:00","tags":[]}]}',
			],
		];
	}

	public function testObjectNormalizationIsRecursiveAndImmutable(): void
	{
		$inner = (object) ['b' => 1];
		$data = (object) ['a' => $inner];

		$result = $this->normalizer->normalize($data);

		$this->assertInstanceOf(stdClass::class, $result);
		$this->assertInstanceOf(stdClass::class, $result->a);
		$this->assertNotSame($inner, $result->a, 'normalization must build fresh objects, not reuse the source');
		$this->assertSame(1, $inner->b, 'source object must be untouched');
	}
}
