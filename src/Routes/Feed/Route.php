<?php

namespace Hoo\WordPressPluginFramework\Routes\Feed;

use Closure;
use Hoo\WordPressPluginFramework\{
	Routes\RouteInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Pipeline\PipelineFactoryInterface,
};

readonly class Route implements RouteInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $name,
		protected Closure $closure,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			'init',
			$this->addFeed(...),
			10,
			0,
		);
	}

	public function up(): void
	{
		$this->addFeed();
	}

	public function down(): void
	{

	}

	protected function addFeed(): void
	{
		add_feed(
			$this->name,
			$this->callback(...),
		);
	}

	protected function callback(): void
	{
		$pipeline = $this->pipelineFactory->createFromServer();

		$response = $pipeline(($this->closure)(...));
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
				'Content-Type' => 'application/xml',
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
