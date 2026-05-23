<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

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

	public function fromException(Http\Request\RequestInterface $request, Http\Exceptions\Exception $exception): ?HeadersInterface
	{
		$headers = $exception->getHeaders();

		$accept = $request->headers()->accept();
		if ($accept !== null) {
			$headers['content-type'] = $accept;
		}

		if ($headers === null) {
			return null;
		}

		return $this->from($headers);
	}

	public function fromThrowable(Http\Request\RequestInterface $request, Throwable $throwable): ?HeadersInterface
	{
		$headers = null;

		$accept = $request->headers()->accept();
		if ($accept !== null) {
			$headers['content-type'] = $accept;
		}

		if ($headers === null) {
			return null;
		}

		return $this->from($headers);
	}
}
