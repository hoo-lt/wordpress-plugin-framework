<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\AdminAjax;

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
			$middlewares
		);
	}

	public function hooks(): array
	{
		$closure = function (): void {
			$response = $this->pipeline
				->withMiddlewares(...$this->middlewares)
				->catch($this->handler->handle(...))
			(($this->closure)(...));

			if (!$response instanceof ResponseInterface) {
				$response = $this->response($response);
			}

			$this->statusCode($response);
			$this->headers($response);
			$this->body($response);

			exit();
		};

		return [
			$this->hookFactory->action("wp_ajax_{$this->action}", $closure),
			$this->hookFactory->action("wp_ajax_nopriv_{$this->action}", $closure),
		];
	}

	protected function response(array|string|null $body): ResponseInterface
	{
		return $this->responseFactory->from(
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