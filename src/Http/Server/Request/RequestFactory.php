<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Server\ServerInterface,
	Http\Url\UrlFactoryInterface,
	Uuid\UuidFactoryInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UuidFactoryInterface $uuidFactory,
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected ServerInterface $server,
	) {
	}

	public function create(string $method, string $url, ?array $headers = null, array|string|null $body = null): RequestInterface
	{
		$uuid = $this->uuidFactory->create();
		$method = Method::from($method);
		$url = $this->urlFactory->create($url);
		$headers = $this->headersFactory->tryCreate($headers);
		$body = $this->bodyFactory->tryCreate(
			$body,
			$headers->contentType(),
		);

		return new Request($uuid, $method, $url, $headers, $body);
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
