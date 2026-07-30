<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

interface NegotiatorInterface
{
	public function negotiate(?string $accept, mixed $decoded): MediaTypeInterface;
	public function tryNegotiate(?string $accept, mixed $decoded): ?MediaTypeInterface;
}
