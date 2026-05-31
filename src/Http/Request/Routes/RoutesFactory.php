<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Routes;

readonly class RoutesFactory implements RoutesFactoryInterface
{
	public function from(array $routes): RoutesInterface
	{
		return new Routes($routes);
	}

	public function tryFrom(?array $routes): ?RoutesInterface
	{
		if ($routes === null) {
			return null;
		}

		return $this->from($routes);
	}
}
