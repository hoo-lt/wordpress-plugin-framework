<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http\Url\Query\QueryFactoryInterface;

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected QueryFactoryInterface $queryFactory,
	) {
	}

	public function create(string $url): UrlInterface
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
			$this->queryFactory->tryCreate($url['query'] ?? null),
		);
	}
}