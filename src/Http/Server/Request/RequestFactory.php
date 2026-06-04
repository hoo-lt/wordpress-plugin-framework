<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Server\ServerInterface,
	Http\Url\UrlFactoryInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected ServerInterface $server,
	) {
	}

	public function create(string $method, string $url, ?array $headers = null, array|string|null $body = null): RequestInterface
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

	public function createFromServer(): RequestInterface
	{
		return $this->create(
			$this->server->method(),
			$this->server->url(),
			$this->server->headers(),
			$this->server->body(),
		);
	}
}
