<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Countable;
use IteratorAggregate;

interface DecodersInterface extends IteratorAggregate, Countable
{
	public function first(): DecoderInterface;
	public function last(): DecoderInterface;

	public function filterByType(mixed $encoded): static;
	public function filterByContentType(string $contentType): static;
}
