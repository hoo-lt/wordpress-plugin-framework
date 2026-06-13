<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookFactoryInterface,
	Http\Method\Method,
	Http\Server\Request\Routes\RoutesFactoryInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Exceptions\Handler\HandlerInterface,
	Pipeline\Middlewares\MiddlewaresBuilder,
};

readonly class RouteFactory implements RouteFactoryInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected HandlerInterface $handler,
		protected RoutesFactoryInterface $routesFactory,
		protected MiddlewaresBuilder $middlewaresBuilder,
	) {
	}

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresBuilderClosure = null): RouteInterface
	{
		return new AdminAjax\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$action,
			$closure,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	public function feed(string $name, Closure $closure, ?Closure $middlewaresBuilderClosure = null): RouteInterface
	{
		return new Feed\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$name,
			$closure,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresBuilderClosure = null): RouteInterface
	{
		return new Rest\Route(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$this->routesFactory,
			$routeNamespace,
			$route,
			$closure,
			$method,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	protected function buildMiddlewares(Closure $closures): array
	{
		$middlewaresBuilder = $closures($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilder) {
			throw new RouteFactoryException('The middlewares builder closure must return an instance of MiddlewaresBuilder.');
		}

		return $middlewaresBuilder->build();
	}

	protected function tryBuildMiddlewares(?Closure $closures): array
	{
		if ($closures === null) {
			return [];
		}

		return $this->buildMiddlewares($closures);
	}
}
