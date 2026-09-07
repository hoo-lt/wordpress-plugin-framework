<?php

namespace Hoo\WordPressPluginFramework\Http\ContentNegotiation;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface,
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
		$accept = $this->acceptFactory->tryCreate($request->headers()->get('accept'));
		if ($accept !== null) {
			$responses = $responses
				->filter(fn(ResponseInterface $response) => $this->q($accept, $response) > 0)
				->sort(fn(ResponseInterface $a, ResponseInterface $b) => $this->q($accept, $b) <=> $this->q($accept, $a));
		}

		if ($responses->count() === 0) {
			return null;
		}

		return $responses
			->first()
			->withHeaders(fn($headers) => $responses->count() === 1 ? $headers->without('vary') : $headers->with('vary', 'accept'));
	}

	protected function q(AcceptInterface $accept, ResponseInterface $response): float
	{
		return $accept->q($this->mediaTypeFactory->create($response->headers()->get('content-type')));
	}
}