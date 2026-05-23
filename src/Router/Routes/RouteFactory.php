<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker,
	Http,
	Json,
	Pipeline,
};

readonly class RouteFactory implements RouteFactoryInterface
{
	public function __construct(
		protected Hooker\Hooks\HookFactoryInterface $hookFactory,
		protected Json\JsonInterface $json,
		protected Pipeline\PipelineInterface $pipeline,
		protected string $namespace,
	) {
	}

	public function feed(string $path, Closure $closure): RouteInterface
	{
		return new Feed\Route(
			$this->pipeline,
			$this->hookFactory,
			"{$this->namespace}/{$path}",
			$closure
		);
	}

	public function rest(string $path, Closure $closure, Http\Method\Method ...$methods): RouteInterface
	{
		return new Rest\Route(
			$this->hookFactory,
			$this->pipeline,
			$this->json,
			$this->namespace,
			$name,
			$methods,
			$closure
		);
	}
}
