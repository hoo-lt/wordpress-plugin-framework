<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\AdminAjax;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Hooker\Hooks\HookFactoryInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface,
	Exceptions\Handler\HandlerInterface,
};

readonly class Route implements RouteInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected HandlerInterface $handler,
		protected string $action,
		protected Closure $closure,
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
			$this->action,
			$this->closure,
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
			$this->action,
			$this->closure,
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
			$this->hookFactory->action("wp_ajax_{$this->action}", $this->callback(...)),
			$this->hookFactory->action("wp_ajax_nopriv_{$this->action}", $this->callback(...)),
		];
	}

	protected function callback(): void
	{
		$pipeline = $this->pipeline
			->withMiddlewares(...$this->middlewares)
			->catch($this->handler->handle(...));

		$response = $pipeline(($this->closure)(...));
		if (!$response instanceof ResponseInterface) {
			$response = $this->createResponse($response);
		}

		$this->statusCode($response);
		$this->headers($response);
		$this->body($response);

		exit();
	}

	protected function createResponse(array|string|null $body): ResponseInterface
	{
		return $this->responseFactory->create(
			200,
			is_array($body) ? [
				'Content-Type' => 'application/json',
			] : null,
			$body
		);
	}

	protected function statusCode(ResponseInterface $response): void
	{
		http_response_code(
			$response->statusCode(),
		);
	}

	protected function headers(ResponseInterface $response): void
	{
		$headers = $response->headers();
		if ($headers === null) {
			return;
		}

		foreach ($headers as $key => $header) {
			header("{$key}: {$header}");
		}
	}

	protected function body(ResponseInterface $response): void
	{
		echo (string) $response->body();
	}
}