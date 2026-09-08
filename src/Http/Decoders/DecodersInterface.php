<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Countable;
use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use IteratorAggregate;

interface DecodersInterface extends IteratorAggregate, Countable
{
	public function first(): DecoderInterface;
	public function last(): DecoderInterface;

	public function filterByType(mixed $encoded): static;
	public function filterByMediaType(MediaTypeInterface $mediaType): static;
}
