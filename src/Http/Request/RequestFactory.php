<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Headers\HeadersFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\Body\BodyFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Request\Body\BodyInterface;
use Hoo\WordPressPluginFramework\Http\Url\UrlFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;

readonly class RequestFactory implements RequestFactoryInterface
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function from(array $headers, ?BodyInterface $body, Method $method, UrlInterface $url): RequestInterface
	{
		return new Request($this->headersFactory->from($headers), $body, $method, $url);
	}

	public function fromServer(array $server, ?string $body): RequestInterface
	{
		$headers = $this->headersFactory->fromServer($server);

		$bodyObject = null;
		if ($body !== null && $body !== '') {
			$bodyObject = $this->bodyFactory->from($body, $headers->value('content-type'));
		}

		return new Request(
			$headers,
			$bodyObject,
			Method::from($server['REQUEST_METHOD']),
			$this->urlFactory->fromServer($server)
		);
	}
}
