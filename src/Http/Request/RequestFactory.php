<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Client\Request\RequestFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Client\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Url\UrlFactoryInterface;

readonly class RequestFactory
{
	public function __construct(
		protected RequestFactoryInterface $requestFactory,
		protected UrlFactoryInterface $urlFactory,
	) {
	}

	public function fromGlobals(array $server, string $input): RequestInterface
	{
		$method  = Method::from($server['REQUEST_METHOD']);
		$headers = $this->extractHeaders($server);
		$url     = $this->urlFactory->fromUrl($this->reconstructUrl($server));

		$body = in_array($method, [Method::Post, Method::Put, Method::Patch], true)
			? ($input !== '' ? $input : null)
			: null;

		return $this->requestFactory->from($headers, $body, $method, $url);
	}

	private function extractHeaders(array $server): array
	{
		$headers = [];

		foreach ($server as $key => $value) {
			if (str_starts_with($key, 'HTTP_')) {
				$headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
			}
		}

		if (isset($server['CONTENT_TYPE'])) {
			$headers['content-type'] = $server['CONTENT_TYPE'];
		}

		return $headers;
	}

	private function reconstructUrl(array $server): string
	{
		$scheme = (!empty($server['HTTPS']) && $server['HTTPS'] !== 'off') ? 'https' : 'http';
		$host   = $server['HTTP_HOST'] ?? $server['SERVER_NAME'] ?? '';
		$uri    = $server['REQUEST_URI'] ?? '/';

		return "{$scheme}://{$host}{$uri}";
	}
}
