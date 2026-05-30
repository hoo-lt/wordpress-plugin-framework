<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookFactoryInterface,
	Http\Method\Method,
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

	public function feed(string $path, Closure $closure): RouteInterface
	{
		return new Feed\Route(
			$this->pipeline,
			$this->hookFactory,
			$path,
			$closure
		);
	}

	public function rest(string $path, Closure $closure, Method ...$methods): RouteInterface
	{
		$path = ltrim($path, '/');

		[
			$routeNamespace,
			$route,
		] = explode('/', $path, 2);

		return new Rest\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$routeNamespace,
			$route,
			$closure,
			$methods,
		);
	}
}
