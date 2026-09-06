<?php

namespace Hoo\WordPressPluginFramework\Http\ContentNegotiation;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

readonly class ContentNegotiator implements ContentNegotiatorInterface
{
	public function __construct(
		protected AcceptFactoryInterface $acceptFactory,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
	) {
	}

	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ?ResponseInterface
	{
		if (!$request->headers()->has('accept')) {
			return $responses->first();
		}

		if ($responses->count() <= 1) {
			return $responses->first();
		}

		$accept = $this->acceptFactory->create($request->headers()->get('accept'));

		$responses = $responses->filter(fn(ResponseInterface $response) => $this->q($accept, $response) > 0);
		if ($responses->count() <= 1) {
			return $responses->first();
		}

		return $responses
			->sort(fn(ResponseInterface $a, ResponseInterface $b) => $this->q($accept, $b) <=> $this->q($accept, $a))
			->first()
			->withHeaders(fn($headers) => $headers->with('vary', 'accept'));
	}

	protected function q(AcceptInterface $accept, ResponseInterface $response): float
	{
		return $accept->q($this->mediaTypeFactory->create($response->headers()->get('content-type')));
	}
}