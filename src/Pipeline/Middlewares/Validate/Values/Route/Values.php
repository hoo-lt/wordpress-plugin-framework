<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values\Route;

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
		$routes = $request->routes();
		if ($routes === null) {
			return null;
		}

		return [
			$this->key => $routes->route($this->key),
		];
	}
}
