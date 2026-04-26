<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http\Url\{
	Query\QueryFactoryInterface,
	Scheme\Scheme,
};

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected QueryFactoryInterface $queryFactory
	) {
	}

	public function fromUrl(string $url): UrlInterface
	{
		$parsed = parse_url($url);
		if ($parsed === false) {
			throw new UrlFactoryException('seriously damaged url');
		}

		return new Url(
			Scheme::from(
				$parsed['scheme'] ?? '',
			),
			$parsed['host'] ?? '',
			$parsed['port'] ?? null,
			$parsed['path'] ?? '',
			$this->queryFactory->fromQuery(
				$parsed['query'] ?? '',
			),
		);
	}

	public function fromServer(array $server): UrlInterface
	{
		$scheme = !empty($server['HTTPS']) && $server['HTTPS'] !== 'off' ? 'https' : 'http';
		$host = $server['HTTP_HOST'] ?? '';
		$pathQuery = $server['REQUEST_URI'] ?? '';

		$url = "{$scheme}://{$host}{$pathQuery}";

		return $this->fromUrl($url);
	}
}