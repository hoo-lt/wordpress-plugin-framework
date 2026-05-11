<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http;

readonly class RequestFactory
{
	public function __construct(
		protected Http\Url\UrlFactoryInterface $urlFactory,
		protected Http\Headers\HeadersFactoryInterface $headersFactory,
		protected Http\Body\BodyFactoryInterface $bodyFactory,
		protected Http\Server\ServerInterface $server,
	) {
	}

	public function from(string $method, string $url, ?array $headers, ?string $body): RequestInterface
	{
		$url = $this->urlFactory->from($url);
		$headers = $headers ? $this->headersFactory->from($headers) : null;
		$body = $body ? $this->bodyFactory->from(
			$headers->contentType(),
			$body
		) : null;

		return new Request(
			Http\Method\Method::from(
				$method
			),
			$url,
			$headers,
			$body,
		);
	}

	public function fromServer(): RequestInterface
	{
		return new Request(
			Http\Method\Method::from(
				$this->server->method(),
			),
			$this->urlFactory->fromServer(),
			$this->headersFactory->fromServer(),
			$this->bodyFactory->fromServer(),
		);
	}
}
