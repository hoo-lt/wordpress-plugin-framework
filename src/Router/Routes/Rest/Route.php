<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\Rest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Hooker\Hooks\HookInterface,
	Hooker\Hooks\HookFactoryInterface,
	Http\KeyValue\KeyValueInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface,
	Exceptions\HasStatusCodeInterface,
	Exceptions\HasMessagesInterface,
};
use Throwable;
use WP_REST_Response;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
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
						->catch(function (RequestInterface $request, Throwable $throwable): ResponseInterface {
							$statusCode = $throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500;

							$headers = [
								'Content-Type' => 'application/json',
							];

							$body = [
								'message' => $throwable->getMessage(),
								'code' => $throwable->getCode(),
							];

							$messages = $throwable instanceof HasMessagesInterface ? $throwable->getMessages() : null;
							if ($messages !== null) {
								$body = [
									...$body,
									'messages' => $messages->toArray(),
								];
							}

							return $this->responseFactory->from($statusCode, $headers, $body);
						})
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