<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Method\Method,
	Http\Server\Request\Routes\RoutesFactoryInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Exceptions\Handler\HandlerInterface,
	Pipeline\Middlewares\MiddlewaresFactoryInterface,
};

readonly class RoutesBuilder implements RoutesBuilderInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected HandlerInterface $handler,
		protected RoutesFactoryInterface $routesFactory,
		protected MiddlewaresFactoryInterface $middlewaresFactory,
		protected array $routes = [],
	) {
	}

	public function routes(): array
	{
		return $this->routes;
	}

	public function withRoutes(RouteInterface ...$routes): static
	{
		return new static($this->responseFactory, $this->pipeline, $this->handler, $this->routesFactory, $this->middlewaresFactory, $routes);
	}

	public function withoutRoutes(): static
	{
		return new static($this->responseFactory, $this->pipeline, $this->handler, $this->routesFactory, $this->middlewaresFactory, []);
	}

	public function withRoute(RouteInterface $route): static
	{
		return $this->withRoutes(...$this->routes, $route);
	}

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withRoute(
			new AdminAjax\Route($this->responseFactory, $pipeline, $action, $closure)
		);
	}

	public function feed(string $name, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withRoute(
			new Feed\Route($this->responseFactory, $pipeline, $name, $closure)
		);
	}

	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresClosure = null): static
	{
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withRoute(
			new Rest\Route($this->responseFactory, $pipeline, $this->routesFactory, $routeNamespace, $route, $closure, $method)
		);
	}

	public function build(): array
	{
		return $this->routes;
	}

	protected function pipeline(?Closure $closure): PipelineInterface
	{
		$middlewares = $this->middlewaresFactory->tryCreate($closure);
		return $middlewares !== null
			? $this->pipeline
				->withMiddlewares($middlewares)
				->catch($this->handler->handle(...))
			: $this->pipeline
				->withoutMiddlewares()
				->catch($this->handler->handle(...));
	}
}
