<?php

namespace Hoo\WordPressPluginFramework\Route\Rest;

use Closure;
use Hoo\WordPressPluginFramework\Route\RouteInterface;
use Hoo\WordPressPluginFramework\Route\Rest\Method\Method;
use Hoo\WordPressPluginFramework\Hook\HookInterface;
use Hoo\WordPressPluginFramework\Hook\Action\Hook;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $namespace,
		protected string $route,
		protected Method $method,
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): RouteInterface
	{
		return new self(
			$this->pipeline,
			$this->namespace,
			$this->route,
			$this->method,
			$this->closure,
			$middlewares
		);
	}

	public function hook(): HookInterface
	{
		return new Hook(
			$this->pipeline,
			'rest_api_init',
			$this->closure()
		);
	}

	public function closure(): Closure
	{
		return fn() => register_rest_route(
			$this->namespace,
			$this->route,
			[
				'methods' => $this->method->value,
				'callback' => fn(mixed ...$args) => $this->pipeline
					->withMiddlewares(...$this->middlewares)
				(fn() => ($this->closure)(...$args)),
				'permission_callback' => fn() => true,
			]
		);
	}
}
