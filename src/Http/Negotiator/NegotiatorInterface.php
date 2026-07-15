<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\AcceptInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

interface NegotiatorInterface
{
	public function negotiate(?AcceptInterface $accept, mixed $decoded): MediaTypeInterface;
	public function tryNegotiate(?AcceptInterface $accept, mixed $decoded): ?MediaTypeInterface;
}
