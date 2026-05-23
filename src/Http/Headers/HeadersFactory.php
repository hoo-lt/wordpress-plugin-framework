<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Http;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function __construct(
		protected Http\Server\ServerInterface $server,
	) {
	}

	public function from(array $headers): HeadersInterface
	{
		return new Headers($headers);
	}

	public function fromServer(): ?HeadersInterface
	{
		$headers = $this->server->headers();
		if ($headers === null) {
			return null;
		}

		return $this->from($headers);
	}

	public function fromException(Http\Exceptions\Exception $exception): ?HeadersInterface
	{
		$headers = $contentType === null ? $exception->getHeaders() : [
			'Content-Type' => $contentType,
		];
		$headers = $exception->getHeaders() ?? [];


	}

	public function fromThrowable(Throwable $throwable): ?HeadersInterface
	{
		return $this->from(
			500,
			null,
			[
				'message' => $throwable->getMessage(),
				'code' => $throwable->getCode(),
			]
		);
	}
}
