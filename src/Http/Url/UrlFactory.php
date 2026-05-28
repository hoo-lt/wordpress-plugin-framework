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
			Scheme\Scheme::from(
				isset($url['scheme']) ? $url['scheme'] : '',
			),
			isset($url['host']) ? $url['host'] : '',
			isset($url['port']) ? $url['port'] : null,
			isset($url['path']) ? $url['path'] : '',
			isset($url['query']) ? $this->queryFactory->from($url['query']) : null,
		);
	}
}