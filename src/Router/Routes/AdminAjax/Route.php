<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\AdminAjax;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
};

readonly class Route implements RouteInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected string $action,
		protected Closure $closure,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			"wp_ajax_{$this->action}",
			$this->callback(...),
			10,
			0,
		);

		add_action(
			"wp_ajax_nopriv_{$this->action}",
			$this->callback(...),
			10,
			0,
		);
	}

	public function up(): void
	{

	}
	
	public function down(): void
	{

	}

	protected function callback(): void
	{
		$response = ($this->pipeline)(($this->closure)(...));
		if (!$response instanceof ResponseInterface) {
			$response = $this->createResponse($response);
		}

		$this->statusCode($response);
		$this->headers($response);
		$this->body($response);

		exit();
	}

	protected function createResponse(object|array|string|float|int|bool|null $body): ResponseInterface
	{
		return $this->responseFactory->create(
			200,
			[
				'Content-Type' => is_array($body) || is_object($body) ? 'application/json' : 'text/html',
			],
			$body,
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
		foreach ($headers as $key => $header) {
			header("{$key}: {$header}");
		}
	}

	protected function body(ResponseInterface $response): void
	{
		echo (string) $response->body();
	}
}