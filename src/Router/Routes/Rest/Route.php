<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookFactoryInterface;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;
use Hoo\WordPressPluginFramework\Router\Routes\RouteInterface;
use WP_Error;

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
				'callback' => function (mixed ...$args) {
					try {
						return $this->pipeline
							->withMiddlewares(...$this->middlewares)
						(fn() => ($this->closure)(...$args));
					} catch (MiddlewareException $middlewareException) {
						return new WP_Error(
							'middleware_exception',
							$middlewareException->getMessage(),
							[
								'status' => 400
							],
						);
					}
				},
				'permission_callback' => fn() => true,
			]
		));
	}
}
