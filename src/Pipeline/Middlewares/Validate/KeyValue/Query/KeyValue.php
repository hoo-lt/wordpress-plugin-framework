<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue\Query;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
};

readonly class KeyValue implements KeyValueInterface
{
	public function __construct(
		protected string $key,
	) {
	}

	public function key(): string
	{
		return $this->key;
	}

	public function values(RequestInterface $request): ?array
	{
		return $request->queryValues($this->key);
	}

	public function value(RequestInterface $request): mixed
	{
		return $request->queryValue($this->key);
	}
}
