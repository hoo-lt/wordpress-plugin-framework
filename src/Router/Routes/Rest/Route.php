<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\Router\Routes\RouteInterface;
use Hoo\WordPressPluginFramework\Router\Routes\Rest\Method\Method;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookFactoryInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected HookFactoryInterface $hookFactory,
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
			$this->hookFactory,
			$this->namespace,
			$this->route,
			$this->method,
			$this->closure,
			$middlewares
		);
	}

	public function hook(): HookInterface
	{
		return $this->hookFactory->action('rest_api_init', fn() => register_rest_route(
			$this->namespace,
			$this->route,
			[
				'methods' => $this->method->value,
				'callback' => fn(mixed ...$args) => $this->pipeline
					->withMiddlewares(...$this->middlewares)
				(fn() => ($this->closure)(...$args)),
				'permission_callback' => fn() => true,
			]
		));
	}
}
