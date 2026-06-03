<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values\BodyQuery;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\Values\ValuesInterface,
};

readonly class Values implements ValuesInterface
{
	public function __construct(
		protected string $key,
	) {
	}

	public function __invoke(RequestInterface $request): ?array
	{
		return $request->bodyQueryValues($this->key);
	}
}
