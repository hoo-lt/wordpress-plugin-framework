<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Feed;

use Closure;
use Hoo\WordPressPluginFramework\Router\Routes\RouteInterface;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookFactoryInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected HookFactoryInterface $hookFactory,
		protected string $name,
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): RouteInterface
	{
		return new self(
			$this->pipeline,
			$this->hookFactory,
			$this->name,
			$this->closure,
			$middlewares
		);
	}

	public function hook(): HookInterface
	{
		return $this->hookFactory->action('init', fn() => add_feed(
			$this->name,
			fn(mixed ...$args) => $this->pipeline
				->withMiddlewares(...$this->middlewares)
			(fn() => ($this->closure)(...$args))
		));
	}
}
