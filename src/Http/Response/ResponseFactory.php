<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected Http\Request\RequestInterface $request,
		protected Http\Headers\HeadersFactoryInterface $headersFactory,
		protected Http\Body\BodyFactoryInterface $bodyFactory,
	) {
	}

	public function from(int $statusCode, ?array $headers = null, mixed $body = null): ResponseInterface
	{
		$headers = $headers ? $this->headersFactory->from($headers) : null;
		$body = $body ? $this->bodyFactory->from(
			$headers->contentType(),
			$body,
		) : null;

		return new Response(
			$statusCode,
			$headers,
			$body,
		);
	}

	public function fromException(Http\Exceptions\Exception $exception): ResponseInterface
	{
		return $this->from(
			$exception->getStatusCode(),
			$this->headersFactory->fromException($exception),
			$this->bodyFactory->fromException($exception),
		);
	}

	public function fromThrowable(Throwable $throwable): ResponseInterface
	{
		return $this->from(
			$throwable->getStatusCode(),
			$this->headersFactory->fromThrowable($throwable),
			$this->bodyFactory->fromThrowable($throwable),
		);
	}
}