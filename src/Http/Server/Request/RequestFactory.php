<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Method\Method,
	Http\Server\ServerInterface,
	Http\Url\UrlFactoryInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	protected const CONTENT_TYPE = 'application/octet-stream';

	public function __construct(
		protected UuidInterface $uuid,
		protected ServerInterface $server,
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(): RequestInterface
	{
		return new Request(
			$this->uuid,
			$this->createMethod(),
			$this->createUrl(),
			$this->createHeaders(),
			$this->tryCreateBody(),
		);
	}

	protected function createMethod(): Method
	{
		$method = $this->server->method();

		return Method::create($method);
	}

	protected function createUrl(): UrlInterface
	{
		$url = $this->server->url();

		return $this->urlFactory->create($url);
	}

	protected function createHeaders(): HeadersInterface
	{
		$headers = $this->server->headers();

		return $this->headersFactory->create($headers);
	}

	protected function tryCreateBody(): ?BodyInterface
	{
		$body = $this->server->body();
		if ($body === null) {
			return null;
		}

		$contentType = $this->server->contentType();
		if ($contentType === null) {
			$contentType = self::CONTENT_TYPE;
		}

		return $this->bodyFactory->createFromEncoded($body, $contentType);
	}
}
