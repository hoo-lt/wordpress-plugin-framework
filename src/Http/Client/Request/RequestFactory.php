<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
	Http\Url\UrlInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(string $method, string $url, array $headers = [], mixed $body = null): RequestInterface
	{
		$method = $this->method($method);
		$url = $this->url($url);
		$headers = $this->headers($headers);
		$body = $this->body($headers, $body);

		return new Request($method, $url, $headers, $body);
	}

	protected function method(string $method): Method
	{
		$method = Method::tryFrom($method);
		if ($method === null) {
			throw new RequestFactoryException('invalid method');
		}

		return $method;
	}

	protected function url(string $url): UrlInterface
	{
		return $this->urlFactory->create($url);
	}

	protected function headers(array $headers): HeadersInterface
	{
		return $this->headersFactory->create($headers);
	}

	protected function body(HeadersInterface $headers, mixed $body): ?BodyInterface
	{
		$contentLength = $headers->contentLength();
		if ($contentLength === null) {
			return null;
		}

		$contentType = $headers->contentType();
		if ($contentType === null) {
			//throw new 415
		}

		return $this->bodyFactory->createFromDecoded($body, $contentType);
	}
}
