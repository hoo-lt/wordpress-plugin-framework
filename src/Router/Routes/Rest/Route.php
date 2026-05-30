<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Hooker\Hooks\HookFactoryInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface,
	Exceptions\Handler\HandlerInterface,
};
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected HandlerInterface $handler,
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
			$this->pipeline,
			$this->handler,
			$this->routeNamespace,
			$this->route,
			$this->closure,
			$this->methods,
			$middlewares
		);
	}

	public function hooks(): array
	{
		return [
			$this->hookFactory->action('rest_api_init', fn() => register_rest_route(
				$this->routeNamespace,
				$this->route,
				[
					'methods' => array_map(fn($method) => $method->value, $this->methods),
					'callback' => function (): WP_REST_Response {
						$response = $this->pipeline
							->withMiddlewares(...$this->middlewares)
							->catch($this->handler->handle(...))
						(($this->closure)(...));

						if (!$response instanceof ResponseInterface) {
							$response = $this->response($response);
						}

						$statusCode = $response->statusCode();
						$headers = $response->headers();
						$body = $response->body();

						return new WP_REST_Response(
							(string) $body,
							$statusCode,
							$headers,
						);
					},
					'permission_callback' => fn() => true,
				]
			)),

		];
	}

	protected function response(array|string|null $body): ResponseInterface
	{
		return $this->responseFactory->from(
			200,
			[
				'Content-Type' => 'application/json',
			],
			$body,
		);
	}
}