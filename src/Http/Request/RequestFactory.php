<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Body\BodyFactoryInterface,
	Http\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Server\ServerInterface,
	Http\Url\UrlFactoryInterface,
};

readonly class RequestFactory
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected ServerInterface $server,
	) {
	}

	public function from(string $method, string $url, ?array $headers = null, array|string|null $body = null): RequestInterface
	{
		$method = Method::from($method);
		$url = $this->urlFactory->from($url);
		$headers = $this->headersFactory->tryFrom($headers);
		$body = $this->bodyFactory->tryFrom(
			$body,
			$headers->contentType(),
		);

		return new Request($method, $url, $headers, $body);
	}

	public function fromServer(): RequestInterface
	{
		return $this->from(
			$this->server->method(),
			$this->server->url(),
			$this->server->headers(),
			$this->server->body(),
		);
	}
}
