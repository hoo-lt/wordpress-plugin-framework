<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineFactoryInterface,
	Http\Method\Method,
};
use WP_REST_Request;
use WP_REST_Response;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $routeNamespace,
		protected string $route,
		protected Closure $closure,
		protected Method $method,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			'rest_api_init',
			$this->restApiInit(...),
			10,
			0,
		);

		add_filter(
			'rest_pre_serve_request',
			$this->restPreServeRequest(...),
			10,
			3,
		);
	}

	public function up(): void
	{

	}

	public function down(): void
	{

	}

	protected function restApiInit(): void
	{
		register_rest_route(
			$this->routeNamespace,
			$this->route,
			[
				'methods' => [
					$this->method->value,
				],
				'callback' => $this->callback(...),
				'permission_callback' => $this->permissionCallback(...),
			],
		);
	}

	protected function restPreServeRequest(bool $served, WP_REST_Response $response, WP_REST_Request $request): bool
	{
		if ($request->get_route() !== "/{$this->routeNamespace}/{$this->route}") {
			return $served;
		}

		echo $response->get_data();

		return true;
	}

	protected function callback(WP_REST_Request $request): WP_REST_Response
	{
		$pipeline = $this->pipelineFactory->create(
			$this->method($request),
			$this->url($request),
			$this->headers($request),
			$this->body($request),
			$this->routes($request),
		);

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

	protected function permissionCallback(WP_REST_Request $request): bool
	{
		return true;
	}

	protected function method(WP_REST_Request $request): string
	{
		return $request->get_method();
	}

	protected function url(WP_REST_Request $request): string
	{
		return add_query_arg(
			$request->get_query_params(),
			rest_url(
				$request->get_route(),
			),
		);
	}

	protected function headers(WP_REST_Request $request): array
	{
		$headers = [];

		foreach ($request->get_headers() as $key => $values) {
			$headers[str_replace('_', '-', $key)] = implode(',', $values);
		}

		return $headers;
	}

	protected function body(WP_REST_Request $request): ?string
	{
		return $request->get_body();
	}

	protected function routes(WP_REST_Request $request): array
	{
		return $request->get_url_params();
	}

	protected function createResponse(object|array|string|float|int|bool|null $body): ResponseInterface
	{
		return $this->responseFactory->create(
			200,
			[
				'Content-Type' => 'application/json',
			],
			$body,
		);
	}
}
