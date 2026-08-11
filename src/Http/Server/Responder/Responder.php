<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Responder;

use Hoo\WordPressPluginFramework\{
	Http\Negotiator\NegotiatorInterface,
	Http\Semantics\Accept\AcceptFactoryInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
	Http\Server\Request\RequestInterface,
	Http\Response\ResponseFactoryInterface,
	Http\Response\ResponseInterface,
	View\ViewInterface,
};

readonly class Responder implements ResponderInterface
{
	public function __construct(
		protected NegotiatorInterface $negotiator,
		protected AcceptFactoryInterface $acceptFactory,
		protected ResponseFactoryInterface $responseFactory,
	) {
	}

	public function respond(RequestInterface $request, mixed $response): ResponseInterface
	{
		if ($response instanceof ResponseInterface) {
			return $response;
		}

		$mediaType = $this->negotiator->negotiate(
			$this->acceptFactory->tryCreate($request->headers()->accept()),
		);

		$body = $this->body($mediaType, $response);

		return $this->responseFactory->create(
			200,
			[
				'Content-Type' => $mediaType,
				'Vary' => 'accept',
			],
			$body,
		);
	}

	protected function body(MediaTypeInterface $mediaType, mixed $response): mixed
	{
		if (!$response instanceof ViewInterface) {
			return $response;
		}

		if (
			$mediaType->type() === 'text' &&
			$mediaType->subtype() === 'html'
		) {
			return $response->render();
		}

		return $response->model();
	}
}
