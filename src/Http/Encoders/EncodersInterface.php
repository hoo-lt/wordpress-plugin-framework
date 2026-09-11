<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Closure;
use Countable;
use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use IteratorAggregate;

interface EncodersInterface extends IteratorAggregate, Countable
{
	public function with(EncoderInterface $encoder): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;

	public function first(): EncoderInterface;
	public function last(): EncoderInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function filterByType(mixed $decoded): static;
	public function filterByMediaType(MediaTypeInterface $mediaType): static;
	public function mapMediaType(MediaTypeInterface $mediaType): static;
}
