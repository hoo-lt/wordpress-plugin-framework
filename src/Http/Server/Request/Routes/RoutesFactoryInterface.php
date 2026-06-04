<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request\Routes;

interface RoutesFactoryInterface
{
	public function from(array $routes): RoutesInterface;
	public function tryFrom(?array $routes): ?RoutesInterface;
}
