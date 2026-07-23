<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(string $method, string $url, array $headers = [], object|array|string|float|int|bool|null $body = null): RequestInterface
	{
		$method = Method::from($method);
		$url = $this->urlFactory->create($url);
		$headers = $this->headersFactory->create($headers);
		$body = $this->bodyFactory->tryCreateFromDecoded(
			$body,
			$headers->contentType(),
		);

		return new Request($method, $url, $headers, $body);
	}
}
