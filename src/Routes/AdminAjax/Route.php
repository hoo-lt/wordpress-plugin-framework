<?php

namespace Hoo\WordPressPluginFramework\Routes\AdminAjax;

use Closure;
use Hoo\WordPressPluginFramework\{
	Routes\RouteInterface,
	Http\Server\Request\RequestInterface,
	Http\Server\Request\RequestFactoryInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
};

readonly class Route implements RouteInterface
{
	protected RequestInterface $request;
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestFactoryInterface $requestFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $action,
		protected Closure $closure,
		protected ?Closure $middlewaresBuilderClosure = null,
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
		$pipeline = $this->pipeline();

		$response = $pipeline(($this->closure)(...));
		if (!$response instanceof ResponseInterface) {
			$response = $this->createResponse($response);
		}

		$this->statusCode($response);
		$this->headers($response);
		$this->body($response);

		exit();
	}

	protected function pipeline(): PipelineInterface
	{
		$request = $this->request();

		return $this->pipeline ??= $this->pipelineFactory->create($request, $this->middlewaresBuilderClosure);
	}

	protected function request(): RequestInterface
	{
		return $this->request ??= $this->requestFactory->createFromServer();
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