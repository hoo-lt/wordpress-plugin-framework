<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Countable;
use IteratorAggregate;

interface EncodersInterface extends IteratorAggregate, Countable
{
	public function first(): EncoderInterface;
	public function last(): EncoderInterface;

	public function filterByType(mixed $decoded): static;
	public function filterByContentType(string $contentType): static;
}
