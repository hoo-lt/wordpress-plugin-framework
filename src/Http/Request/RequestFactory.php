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

	public function from(string $method, string $url, ?array $headers, array|string|null $body): RequestInterface
	{
		$url = $this->urlFactory->from($url);
		$headers = $this->headersFactory->tryFrom($headers);
		$body = $this->bodyFactory->tryFrom(
			$body,
			$headers->contentType(),
		);

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
		$url = $this->urlFactory->from(
			$this->server->url(),
		);

		$method = Http\Method\Method::from(
			$this->server->method(),
		);

		$headers = $this->headersFactory->tryFrom(
			$this->server->headers(),
		);

		$body = $this->bodyFactory->tryFrom(
			$this->server->body(),
			$headers->contentType(),
		);

		return new Request($method, $url, $headers, $body);
	}
}
