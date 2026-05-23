<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker,
	Http,
	Json,
	Pipeline,
	Router,
};
use WP_REST_Response;

readonly class Route implements Router\Routes\RouteInterface
{
	public function __construct(
		protected Hooker\Hooks\HookFactoryInterface $hookFactory,
		protected Http\Response\ResponseFactoryInterface $responseFactory,
		protected Json\JsonInterface $json,
		protected Pipeline\PipelineInterface $pipeline,
		protected string $namespace,
		protected string $path,
		protected array $methods,
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(Pipeline\Middlewares\MiddlewareInterface ...$middlewares): Router\Routes\RouteInterface
	{
		return new self(
			$this->hookFactory,
			$this->responseFactory,
			$this->json,
			$this->pipeline,
			$this->namespace,
			$this->path,
			$this->methods,
			$this->closure,
			$middlewares
		);
	}

	public function hook(): Hooker\Hooks\HookInterface
	{
		return $this->hookFactory->action('rest_api_init', fn() => register_rest_route(
			$this->namespace,
			$this->path,
			[
				'methods' => $this->methods,
				'callback' => function () {
					$response = $this->pipeline
						->withMiddlewares(...$this->middlewares)
						->catchException($this->responseFactory->fromException(...))
						->catchThrowable($this->responseFactory->fromThrowable(...))
					(($this->closure)(...));

					return new WP_REST_Response(
						$this->json->decode(
							$response->body(),
						),
						$response->statusCode(),
						$response->headers(),
					);
				},
				'permission_callback' => fn() => true,
			]
		));
	}
}
