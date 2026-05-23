<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Hooker\Hooks\HookInterface,
	Hooker\Hooks\HookFactoryInterface,
	Http\KeyValue\KeyValueInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseFactoryInterface,
	Json\JsonInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};
use WP_REST_Response;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected JsonInterface $json,
		protected PipelineInterface $pipeline,
		protected string $routeNamespace,
		protected string $route,
		protected Closure $closure,
		protected array $methods,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->hookFactory,
			$this->responseFactory,
			$this->json,
			$this->pipeline,
			$this->routeNamespace,
			$this->route,
			$this->closure,
			$this->methods,
			$middlewares
		);
	}

	public function hook(): HookInterface
	{
		return $this->hookFactory->action('rest_api_init', fn() => register_rest_route(
			$this->routeNamespace,
			$this->route,
			[
				'methods' => array_map(fn($method) => $method->value, $this->methods),
				'callback' => function () {
					$response = $this->pipeline
						->withMiddlewares(...$this->middlewares)
						->catchException($this->responseFactory->fromException(...))
						->catchThrowable($this->responseFactory->fromThrowable(...))
					(($this->closure)(...));

					$response = ($response instanceof ResponseInterface) ? $response : $this->responseFactory->from(
						200,
						[
							'Content-Type' => 'application/json',
						],
						$response,
					);

					$statusCode = $response->statusCode();
					$headers = $response->headers();
					$body = $response->body();

					return new WP_REST_Response(
						$body instanceof KeyValueInterface ? $body->toArray() : (string) $body,
						$statusCode,
						$headers->toArray(),
					);
				},
				'permission_callback' => fn() => true,
			]
		));
	}
}