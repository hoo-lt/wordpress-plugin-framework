<?php

namespace Hoo\WordPressPluginFramework\Http\ContentNegotiation;

use Hoo\WordPressPluginFramework\{
	Http\Exceptions\NotAcceptable\Exception as NotAcceptableException,
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Message\Headers\ContentType\ContentTypeFactoryInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

readonly class ContentNegotiator implements ContentNegotiatorInterface
{
	public function __construct(
		protected AcceptFactoryInterface $acceptFactory,
		protected ContentTypeFactoryInterface $contentTypeFactory,
	) {
	}

	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface
	{
		$acceptable = $this->acceptable($request, $responses);
		if ($acceptable->count() === 0) {
			throw new NotAcceptableException('no acceptable representation', 'content_negotiator_error');
		}

		return $this->represent($acceptable, $responses);
	}

	public function tryNegotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface
	{
		$acceptable = $this->acceptable($request, $responses);
		if ($acceptable->count() === 0) {
			$acceptable = $responses;
		}

		return $this->represent($acceptable, $responses);
	}

	protected function acceptable(RequestInterface $request, ResponsesInterface $responses): ResponsesInterface
	{
		if ($responses->count() === 0) {
			throw new ContentNegotiatorException('no representations available');
		}

		$accept = $request->headers()->get('accept');
		if ($accept === null) {
			return $responses;
		}

		$accept = $this->acceptFactory->create($accept);

		return $responses
			->filter(fn(ResponseInterface $response) => $this->q($accept, $response) > 0)
			->sort(fn(ResponseInterface $a, ResponseInterface $b) => $this->q($accept, $b) <=> $this->q($accept, $a));
	}

	protected function represent(ResponsesInterface $acceptable, ResponsesInterface $responses): ResponseInterface
	{
		return $acceptable
			->first()
			->withHeaders(fn($headers) => $responses->count() > 1 ? $headers->with('vary', 'accept') : $headers->without('vary'));
	}

	protected function q(AcceptInterface $accept, ResponseInterface $response): float
	{
		$contentType = $response->headers()->get('content-type'); //may be null

		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

		return $accept->q($mediaType);
	}
}
