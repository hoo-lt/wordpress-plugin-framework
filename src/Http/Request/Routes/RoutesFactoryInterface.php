<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Routes;

interface RoutesFactoryInterface
{
	public function from(array $routes): RoutesInterface;
	public function tryFrom(?array $routes): ?RoutesInterface;
}
