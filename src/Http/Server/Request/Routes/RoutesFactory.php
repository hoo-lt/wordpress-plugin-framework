<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request\Routes;

readonly class RoutesFactory implements RoutesFactoryInterface
{
	public function create(array $routes): RoutesInterface
	{
		return new Routes($routes);
	}

	public function tryCreate(?array $routes): ?RoutesInterface
	{
		if ($routes === null) {
			return null;
		}

		return $this->create($routes);
	}
}
