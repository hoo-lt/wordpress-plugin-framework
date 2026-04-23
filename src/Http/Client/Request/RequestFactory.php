<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

use Hoo\WordPressPluginFramework\Http\Headers\HeadersFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Url\UrlFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
	) {
	}

	public function from(array $headers, ?string $body, Method $method, UrlInterface $url): RequestInterface
	{
		return new Request($this->headersFactory->from($headers), $body, $method, $url);
	}

	public function fromServer(array $server, ?string $body): RequestInterface
	{
		return new Request(
			$this->headersFactory->fromServer($server),
			$body,
			Method::from($server['REQUEST_METHOD']),
			$this->urlFactory->fromServer($server)
		);
	}
}
