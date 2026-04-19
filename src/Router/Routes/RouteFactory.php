<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookFactoryInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;
use Hoo\WordPressPluginFramework\Http\Method\Method;

readonly class RouteFactory implements RouteFactoryInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected HookFactoryInterface $hookFactory,
		protected string $namespace,
	) {
	}

	public function feed(string $name, Closure $closure): RouteInterface
	{
		return new Feed\Route($this->pipeline, $this->hookFactory, "{$this->namespace}-{$name}", $closure);
	}

	public function rest(string $route, Method $method, Closure $closure): RouteInterface
	{
		return new Rest\Route($this->pipeline, $this->hookFactory, $this->namespace, $route, $method, $closure);
	}
}
