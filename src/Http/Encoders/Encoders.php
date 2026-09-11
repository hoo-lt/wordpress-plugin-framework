<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use ArrayIterator;
use Closure;
use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use Traversable;

readonly class Encoders implements EncodersInterface
{
	public function __construct(
		protected array $encoders,
	) {
		$this->validate($this->encoders);
	}

	public function with(EncoderInterface $encoder): static
	{
		$encoders = $this->encoders;
		$encoders[] = $encoder;

		return new static($encoders);
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

	public function filter(Closure $closure): static
	{
		$encoders = array_filter($this->encoders, $closure);

		return new static($encoders);
	}

	public function map(Closure $closure): static
	{
		$encoders = array_map($closure, $this->encoders);

		return new static($encoders);
	}

	public function sort(Closure $closure): static
	{
		$encoders = $this->encoders;
		usort($encoders, $closure);

		return new static($encoders);
	}

	public function filterByType(mixed $decoded): static
	{
		return $this->filter(fn($encoder) => $encoder->encodesType($decoded));
	}

	public function filterByMediaType(MediaTypeInterface $mediaType): static
	{
		return $this->filter(fn($encoder) => $encoder->encodesMediaType($mediaType));
	}

	public function mapMediaType(MediaTypeInterface $mediaType): static
	{
		return $this->map(fn($encoder) => $encoder->withMediaType($mediaType));
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			array_values($this->encoders),
		);
	}

	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	public function count(): int
	{
		return count($this->encoders);
	}

	protected function validate(array $encoders): void
	{
		foreach ($encoders as $encoder) {
			if (!$encoder instanceof EncoderInterface) {
				throw new EncodersException('must provide encoder interface');
			}
		}
	}
}
