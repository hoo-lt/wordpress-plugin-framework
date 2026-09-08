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

		$accept = $request->headers()->accept();
		if ($accept === null) {
			return $responses;
		}

		return $responses
			->filterByAccept($accept)
			->sortByAccept($accept);
	}

	protected function represent(ResponsesInterface $acceptable, ResponsesInterface $responses): ResponseInterface
	{
		return $acceptable
			->first()
			->withHeaders(fn($headers) => $responses->count() > 1 ? $headers->with('vary', 'accept') : $headers->without('vary'));
	}
}
