<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Server\Request\Routes\RoutesFactoryInterface,
	Http\Server\ServerInterface,
	Http\Url\UrlFactoryInterface,
	Uuid\UuidInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	protected RequestInterface $request;

	public function __construct(
		protected UuidInterface $uuid,
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected RoutesFactoryInterface $routesFactory,
		protected ServerInterface $server,
	) {
	}

	public function create(string $method, string $url, array $headers = [], ?string $body = null, ?array $routes = null): RequestInterface
	{
		$method = Method::from($method);
		$url = $this->urlFactory->create($url);
		$headers = $this->headersFactory->create($headers);
		$body = $this->bodyFactory->tryCreateFromEncoded(
			$body,
			$headers->contentType(),
		);
		$routes = $this->routesFactory->tryCreate($routes);

		return new Request($this->uuid, $method, $url, $headers, $body, $routes);
	}

	public function createFromServer(): RequestInterface
	{
		return $this->request ??= $this->create(
			$this->server->method(),
			$this->server->url(),
			$this->server->headers(),
			$this->server->body(),
		);
	}
}
