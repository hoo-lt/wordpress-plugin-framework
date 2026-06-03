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
	Http\Method\Method,
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
		protected Method $method,
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
			$this->method,
			$middlewares,
		);
	}

	public function withoutMiddlewares(): static
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
			$this->method,
			[],
		);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
	}

	public function hooks(): array
	{
		return [
			$this->hookFactory->action('rest_api_init', fn(WP_REST_Server $server): bool => register_rest_route(
				$this->routeNamespace,
				$this->route,
				[
					'methods' => [
						$this->method,
					],
					'callback' => $this->callback(...),
					'permission_callback' => $this->permissionCallback(...),
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

	protected function callback(WP_REST_Request $request): WP_REST_Response
	{
		$pipeline = $this->pipeline
			->withRequest(
				$this->request->withRoutes(
					$this->routesFactory->from(
						$request->get_url_params(),
					),
				),
			)
			->withMiddlewares(...$this->middlewares)
			->catch($this->handler->handle(...));

		$response = $pipeline(($this->closure)(...));
		if (!$response instanceof ResponseInterface) {
			$response = $this->createResponse($response);
		}

		return new WP_REST_Response(
			(string) $response->body(),
			$response->statusCode(),
			$response->headers(),
		);
	}

	protected function permissionCallback(): bool
	{
		return true;
	}

	protected function createResponse(array|string|null $body): ResponseInterface
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