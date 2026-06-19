<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue\BodyQuery;

use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
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
		return $request->bodyQueryValues($this->key);
	}

	public function value(RequestInterface $request): mixed
	{
		return $request->bodyQueryValue($this->key);
	}
}
