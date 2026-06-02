<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;

readonly class Values implements ValuesInterface
{
	public function __construct(
		protected string $key,
	) {
	}

	public function __invoke(RequestInterface $request): array
	{
		return $request->values($this->key);
	}
}
