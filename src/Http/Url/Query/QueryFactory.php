<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected Helpers\KeyValue\HelperInterface $keyValueHelper,
		protected Http\Coders\Query\CoderInterface $queryCoder,
		protected Http\Server\ServerInterface $server,
	) {
	}

	public function from(string $query): QueryInterface
	{
		return new Query(
			$this->keyValueHelper,
			$this->queryCoder,
			$this->queryCoder->decode($query),
		);
	}

	public function fromServer(): ?QueryInterface
	{
		$query = $this->server->query();
		if ($query === null) {
			return null;
		}

		return $this->from($query);
	}
}