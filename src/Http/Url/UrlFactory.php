<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http\Url\Scheme\Scheme;

readonly class UrlFactory implements UrlFactoryInterface
{
	public function from(Scheme $scheme, string $host, ?int $port, string $path, array $query): UrlInterface
	{
		return new Url($scheme, $host, $port, $path, $query);
	}

	public function fromUrl(string $url): UrlInterface
	{
		$parsed = parse_url($url);
		if ($parsed === false) {
			throw new UrlFactoryException('seriously damaged url');
		}

		if (isset($parsed['query'])) {
			parse_str($parsed['query'], $parsed['query']);
		}

		return new Url(
			Scheme::from($parsed['scheme'] ?? ''),
			$parsed['host'] ?? '',
			$parsed['port'] ?? null,
			$parsed['path'] ?? '',
			$parsed['query'] ?? [],
		);
	}

	public function fromServer(array $server): UrlInterface
	{
		$scheme = isset($server['HTTPS']) ? 'https' : 'http';
		$host = $server['HTTP_HOST'];
		$pathQuery = $server['REQUEST_URI'];

		$url = "{$scheme}://{$host}{$pathQuery}";

		return $this->fromUrl($url);
	}
}