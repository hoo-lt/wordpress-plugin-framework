<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Responder;

use Hoo\WordPressPluginFramework\{
	Http\Negotiator\NegotiatorFactoryInterface,
	Http\Semantics\Accept\AcceptFactoryInterface,
	Http\Response\ResponseFactoryInterface,
};

readonly class ResponderFactory implements ResponderFactoryInterface
{
	public function __construct(
		protected NegotiatorFactoryInterface $negotiatorFactory,
		protected AcceptFactoryInterface $acceptFactory,
		protected ResponseFactoryInterface $responseFactory,
	) {
	}

	public function create(string $mediaType): ResponderInterface
	{
		return new Responder(
			$this->negotiatorFactory->create($mediaType),
			$this->acceptFactory,
			$this->responseFactory,
		);
	}
}
