<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request\Routes;

interface RoutesFactoryInterface
{
	public function create(array $routes): RoutesInterface;
	public function tryCreate(?array $routes): ?RoutesInterface;
}
