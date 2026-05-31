<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookFactoryInterface,
	Http\Method\Method,
	Http\Request\RequestInterface,
	Http\Request\Routes\RoutesFactoryInterface,
	Http\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Exceptions\Handler\HandlerInterface,
};

readonly class RouteFactory implements RouteFactoryInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected HandlerInterface $handler,
		protected RequestInterface $request,
		protected RoutesFactoryInterface $routesFactory,
	) {
	}

	public function adminAjax(string $action, Closure $closure): RouteInterface
	{
		return new AdminAjax\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$action,
			$closure,
		);
	}

	public function feed(string $name, Closure $closure): RouteInterface
	{
		return new Feed\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$name,
			$closure
		);
	}

	public function rest(string $routeNamespace, string $route, Closure $closure, Method ...$methods): RouteInterface
	{
		return new Rest\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$this->request,
			$this->routesFactory,
			$routeNamespace,
			$route,
			$closure,
			$methods,
		);
	}
}
