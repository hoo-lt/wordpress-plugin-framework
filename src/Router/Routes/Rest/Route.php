<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Hooker\Hooks\HookFactoryInterface,
	Http\Request\RequestInterface,
	Http\Request\Routes\RoutesFactoryInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface,
	Exceptions\Handler\HandlerInterface,
};
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
		protected RequestInterface $request,
		protected RoutesFactoryInterface $routesFactory,
		protected string $routeNamespace,
		protected string $route,
		protected Closure $closure,
		protected array $methods,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->handler,
			$this->request,
			$this->routesFactory,
			$this->routeNamespace,
			$this->route,
			$this->closure,
			$this->methods,
			$middlewares,
		);
	}

	public function hooks(): array
	{
		return [
			$this->hookFactory->action('rest_api_init', fn(WP_REST_Server $server): bool => register_rest_route(
				$this->routeNamespace,
				$this->route,
				[
					'methods' => array_map(fn($method) => $method->value, $this->methods),
					'callback' => function (WP_REST_Request $request): WP_REST_Response {
						$response = $this->pipeline
							->withRequest(
								$this->request->withRoutes(
									$this->routesFactory->from(
										$request->get_url_params(),
									),
								),
							)
							->withMiddlewares(...$this->middlewares)
							->catch($this->handler->handle(...))
						(($this->closure)(...));

						if (!$response instanceof ResponseInterface) {
							$response = $this->response($response);
						}

						return new WP_REST_Response(
							(string) $response->body(),
							$response->statusCode(),
							$response->headers(),
						);
					},
					'permission_callback' => fn() => true,
				]
			)),
			$this->hookFactory->filter('rest_pre_serve_request', function (bool $served, WP_REST_Response $response, WP_REST_Request $request, WP_REST_Server $server): bool {
				if ($request->get_route() !== "/{$this->routeNamespace}/{$this->route}") {
					return $served;
				}

				echo $response->get_data();

				return true;
			}),
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