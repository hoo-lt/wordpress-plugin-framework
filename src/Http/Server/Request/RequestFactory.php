<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Server\ServerInterface,
	Http\Url\UrlFactoryInterface,
	Uuid\UuidInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UuidInterface $uuid,
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected ServerInterface $server,
	) {
	}

	public function create(string $method, string $url, ?array $headers = null, ?string $body = null): RequestInterface
	{
		$method = Method::from($method);
		$url = $this->urlFactory->create($url);
		$headers = $this->headersFactory->tryCreate($headers);
		$body = $this->bodyFactory->tryCreateFromEncoded(
			$body,
			$headers?->contentType(),
		);

		return new Request($this->uuid, $method, $url, $headers, $body);
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
