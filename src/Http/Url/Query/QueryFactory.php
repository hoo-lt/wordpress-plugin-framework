<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Helpers\KeyValue\HelperInterface,
	Http\Coders\Query\CoderInterface,
};

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected HelperInterface $helper,
		protected CoderInterface $coder,
	) {
	}

	public function from(array|string $query): QueryInterface
	{
		if (is_string($query)) {
			$query = $this->coder->decode($query);
		}

		return new Query(
			$this->helper,
			$this->coder,
			$query,
		);
	}

	public function tryFrom(array|string|null $query): ?QueryInterface
	{
		if ($query === null) {
			return null;
		}

		return $this->from($query);
	}
}