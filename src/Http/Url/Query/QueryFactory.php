<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected Http\Coders\Query\CoderInterface $queryCoder,
	) {
	}

	public function fromQuery(string $query): QueryInterface
	{
		return new Query(
			$this->arrayHelper,
			$this->queryCoder,
			$this->queryCoder->decode($query),
		);
	}
}