<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\AcceptFactoryInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeFactoryInterface,
};

readonly class NegotiatorFactory implements NegotiatorFactoryInterface
{
	public function __construct(
		protected AcceptFactoryInterface $acceptFactory,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected array $coders,
	) {
	}

	public function create(string $mediaType): NegotiatorInterface
	{
		return new Negotiator($this->acceptFactory, $this->mediaTypeFactory, $mediaType, $this->coders);
	}
}
