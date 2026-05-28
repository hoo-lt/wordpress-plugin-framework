<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected Http\Headers\HeadersFactoryInterface $headersFactory,
		protected Http\Body\BodyFactoryInterface $bodyFactory,
	) {
	}

	public function from(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		$headers = $this->headersFactory->tryFrom($headers);

		$body = $this->bodyFactory->tryFrom(
			$body,
			$headers->contentType(),
		);

		return new Response($statusCode, $headers, $body);
	}

	public function fromThrowable(Http\Request\RequestInterface $request, Throwable $throwable): ResponseInterface
	{
		$accept = $request->headers()?->accept();
		if (!$accept) {
			throw new ResponseFactoryException('cant create response from throwable w/o accept header');
		}

		$statusCode = $throwable instanceof Http\Exceptions\HasStatusCode ? $throwable->getStatusCode() : 500;

		$headers = [
			'content-type' => $accept,
		];

		$body = [
			'message' => $throwable->getMessage(),
			'code' => $throwable->getCode(),
		];

		$messages = $throwable instanceof Http\Exceptions\HasMessages ? $throwable->getMessages() : null;
		if ($messages !== null) {
			$body = [
				...$body,
				'messages' => $messages->toArray(),
			];
		}

		return $this->from($statusCode, $headers, $body);
	}
}