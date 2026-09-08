<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use ArrayIterator;
use Closure;
use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\ContentTypeFactoryInterface;
use Traversable;

readonly class Decoders implements DecodersInterface
{
	public function __construct(
		protected ContentTypeFactoryInterface $contentTypeFactory,
		protected array $decoders,
	) {
	}

	public function first(): DecoderInterface
	{
		$key = array_key_first($this->decoders);
		if ($key === null) {
			throw new DecodersException('no decoder found');
		}

		return $this->decoders[$key];
	}

	public function last(): DecoderInterface
	{
		$key = array_key_last($this->decoders);
		if ($key === null) {
			throw new DecodersException('no decoder found');
		}

		return $this->decoders[$key];
	}

	public function filterByType(mixed $encoded): static
	{
		return $this->filter(fn($decoder) => $decoder->decodesType($encoded));
	}

	public function filterByContentType(string $contentType): static
	{
		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

		return $this->filter(fn($decoder) => $decoder->decodesMediaType($mediaType));
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			array_values($this->decoders),
		);
	}

	public function count(): int
	{
		return count($this->decoders);
	}

	protected function filter(Closure $closure): static
	{
		$decoders = array_filter($this->decoders, $closure);

		return new static($this->contentTypeFactory, $decoders);
	}
}
