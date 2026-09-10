<?php

namespace Hoo\WordPressPluginFramework\Http\ContentNegotiation;

use Hoo\WordPressPluginFramework\{
	Http\Exceptions\NotAcceptable\Exception as NotAcceptableException,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

readonly class ContentNegotiator implements ContentNegotiatorInterface
{
	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface
	{
		$responses = $this->acceptable($request, $responses);
		if ($responses->count() === 0) {
			throw new NotAcceptableException('no acceptable representation', 'content_negotiator_error');
		}

		return $this->response($responses);
	}

	public function tryNegotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface
	{
		$acceptable = $this->acceptable($request, $responses);

		$acceptable = $acceptable->count() === 0 ? $acceptable : $responses;

		return $this->response($acceptable);
	}

	protected function acceptable(RequestInterface $request, ResponsesInterface $responses): ResponsesInterface
	{
		if ($responses->count() === 0) {
			throw new ContentNegotiatorException('no representations available');
		}

		if ($responses->count() === 1) {
			return $responses;
		}

		$accept = $request->headers()->accept();
		if ($accept === null) {
			return $responses;
		}

		return $responses
			->filterByAccept($accept)
			->sortByAccept($accept);
	}

	protected function response(ResponsesInterface $responses): ResponseInterface
	{
		return $responses
			->first()
			->withHeaders(fn($headers) => $headers->with('vary', 'accept'));
	}
}
