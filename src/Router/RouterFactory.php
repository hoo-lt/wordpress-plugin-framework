<?php

namespace Hoo\WordPressPluginFramework\Router;

use Closure;
use Hoo\WordPressPluginFramework\Router\Routes\RoutesBuilderInterface;

readonly class RouterFactory implements RouterFactoryInterface
{
	public function __construct(
		protected RoutesBuilderInterface $routesBuilder,
	) {
	}

	public function create(Closure $closure): RouterInterface
	{
		return new Router(
			$this->routes($closure),
		);
	}

	protected function routes(Closure $closure): array
	{
		$routesBuilder = $closure($this->routesBuilder);
		if (!$routesBuilder instanceof RoutesBuilderInterface) {
			throw new RouterFactoryException('closure must return routes builder instance');
		}

		return $routesBuilder->build();
	}
}
