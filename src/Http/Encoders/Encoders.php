<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use ArrayIterator;
use Closure;
use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\ContentTypeFactoryInterface;
use Traversable;

readonly class Encoders implements EncodersInterface
{
	public function __construct(
		protected ContentTypeFactoryInterface $contentTypeFactory,
		protected array $encoders,
	) {
	}

	public function first(): EncoderInterface
	{
		$key = array_key_first($this->encoders);
		if ($key === null) {
			throw new EncodersException('no encoder found');
		}

		return $this->encoders[$key];
	}

	public function last(): EncoderInterface
	{
		$key = array_key_last($this->encoders);
		if ($key === null) {
			throw new EncodersException('no encoder found');
		}

		return $this->encoders[$key];
	}

	public function filterByType(mixed $decoded): static
	{
		return $this->filter(fn($encoder) => $encoder->encodesType($decoded));
	}

	public function filterByContentType(string $contentType): static
	{
		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

		return $this->filter(fn($encoder) => $encoder->encodesMediaType($mediaType));
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			array_values($this->encoders),
		);
	}

	public function count(): int
	{
		return count($this->encoders);
	}

	protected function filter(Closure $closure): static
	{
		$encoders = array_filter($this->encoders, $closure);

		return new static($this->contentTypeFactory, $encoders);
	}
}
