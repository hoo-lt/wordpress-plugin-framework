<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HooksBuilderInterface,
	Http\Method\Method,
	Http\Server\Request\Routes\RoutesFactoryInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Exceptions\Handler\HandlerInterface,
	Pipeline\Middlewares\MiddlewaresBuilder,
};

readonly class RoutesBuilder implements RoutesBuilderInterface
{
	public function __construct(
		protected HooksBuilderInterface $hooksBuilder,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected HandlerInterface $handler,
		protected RoutesFactoryInterface $routesFactory,
		protected MiddlewaresBuilder $middlewaresBuilder,
		protected array $routes = [],
	) {
	}

	public function routes(): array
	{
		return $this->routes;
	}

	public function withRoutes(RouteInterface ...$routes): static
	{
		return new static($this->hooksBuilder, $this->responseFactory, $this->pipeline, $this->handler, $this->routesFactory, $this->middlewaresBuilder, $routes);
	}

	public function withoutRoutes(): static
	{
		return new static($this->hooksBuilder, $this->responseFactory, $this->pipeline, $this->handler, $this->routesFactory, $this->middlewaresBuilder, []);
	}

	public function withRoute(RouteInterface $route): static
	{
		return $this->withRoutes(...$this->routes, $route);
	}

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withRoute(
			new AdminAjax\Route($this->hooksBuilder, $this->responseFactory, $this->pipeline, $this->handler, $action, $closure, $middlewares)
		);
	}

	public function feed(string $name, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withRoute(
			new Feed\Route($this->hooksBuilder, $this->responseFactory, $this->pipeline, $this->handler, $name, $closure, $middlewares)
		);
	}

	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withRoute(
			new Rest\Route($this->hooksBuilder, $this->responseFactory, $this->pipeline, $this->handler, $this->routesFactory, $routeNamespace, $route, $closure, $method, $middlewares)
		);
	}

	public function build(): array
	{
		return $this->routes;
	}

	protected function buildMiddlewares(Closure $closures): array
	{
		$middlewaresBuilder = $closures($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilder) {
			throw new RoutesBuilderException('The middlewares builder closure must return an instance of MiddlewaresBuilder.');
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
