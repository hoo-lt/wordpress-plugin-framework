<?php

namespace Hoo\WordPressPluginFramework\Routes\AdminAjax;

use Closure;
use Hoo\WordPressPluginFramework\{
	Routes\RouteInterface,
	Http\Server\Responder\ResponderInterface,
	Http\Server\Responder\ResponderFactoryInterface,
	Http\Server\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
};

readonly class Route implements RouteInterface
{
	protected const string MEDIA_TYPE = 'application/json';

	protected ResponderInterface $responder;
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestInterface $request,
		protected ResponderFactoryInterface $responderFactory,
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
		$responder = $this->responder();

		$response = $responder->respond(
			$this->request,
			$pipeline(($this->closure)(...)),
		);

		$this->statusCode($response);
		$this->headers($response);
		$this->body($response);

		exit();
	}

	protected function pipeline(): PipelineInterface
	{
		return $this->pipeline ??= $this->pipelineFactory->create($this->request, $this->middlewaresBuilderClosure);
	}

	protected function responder(): ResponderInterface
	{
		return $this->responder ??= $this->responderFactory->create(self::MEDIA_TYPE);
	}

	protected function statusCode(ResponseInterface $response): void
	{
		$statusCode = $response->statusCode();

		http_response_code($statusCode);
	}

	protected function headers(ResponseInterface $response): void
	{
		$headers = $response->headers();
		foreach ($headers as $name => $header) {
			header("{$name}: {$header}");
		}
	}

	protected function body(ResponseInterface $response): void
	{
		$body = $response->body();

		echo $body;
	}
}