<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValues\Route;

use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValues\KeyValuesInterface,
};

readonly class KeyValues implements KeyValuesInterface
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
		$routes = $request->routes();
		if ($routes === null) {
			return null;
		}

		return [
			$this->key => $routes->route($this->key),
		];
	}
}
