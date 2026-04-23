<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\Helpers;

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
	) {
	}

	public function from(array $query): QueryInterface
	{
		return new Query(
			$this->arrayHelper,
			$query
		);
	}

	public function fromQuery(string $query): QueryInterface
	{
		parse_str($query, $query);

		return new Query(
			$this->arrayHelper,
			$query
		);
	}
}