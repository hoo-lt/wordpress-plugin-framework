<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeFactoryInterface;

readonly class NegotiatorFactory implements NegotiatorFactoryInterface
{
	public function __construct(
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected array $coders,
	) {
	}

	public function create(string $mediaType): NegotiatorInterface
	{
		return new Negotiator($this->mediaTypeFactory->create($mediaType), $this->coders);
	}
}
