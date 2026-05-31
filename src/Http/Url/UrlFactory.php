<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http;

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected Http\Url\Query\QueryFactoryInterface $queryFactory,
	) {
	}

	public function from(string $url): UrlInterface
	{
		$url = parse_url($url);
		if (!is_array($url)) {
			throw new UrlFactoryException('seriously damaged url');
		}

		return new Url(
			Scheme\Scheme::from($url['scheme'] ?? ''),
			$url['host'] ?? '',
			$url['port'] ?? null,
			$url['path'] ?? '',
			$this->queryFactory->tryFrom($url['query'] ?? null),
		);
	}
}