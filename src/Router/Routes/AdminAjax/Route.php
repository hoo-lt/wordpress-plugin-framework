<?php

namespace Hoo\WordPressPluginFramework\Router\Routes\AdminAjax;

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

readonly class Route implements RouteInterface
{
	protected const HEADERS = [
		'Content-Type' => 'application/json'
	];

	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineInterface $pipeline,
		protected string $action, // Название экшена, например: 'my_custom_action'
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->hookFactory,
			$this->responseFactory,
			$this->pipeline,
			$this->action,
			$this->closure,
			$middlewares
		);
	}

	public function hooks(): array
	{
		$callback = function () {
			$response = $this->pipeline
				->withMiddlewares(...$this->middlewares)
				->catch($this->catch(...))
				->handle(($this->closure)(...)); // Предполагаю, что у пайплайна есть метод handle или аналогичный вызов

			if (!$response instanceof ResponseInterface) {
				$response = $this->adaptToResponse($response);
			}

			$this->sendAjaxResponse($response);
		};

		return [
			$this->hookFactory->action("wp_ajax_{$this->action}", $callback),
			$this->hookFactory->action("wp_ajax_nopriv_{$this->action}", $callback),
		];
	}

	protected function adaptToResponse(mixed $body): ResponseInterface
	{
		if (!is_array($body)) {
			return $this->responseFactory->from(500, self::HEADERS, [
				'success' => false,
				'data' => [
					'message' => 'Incorrect controller response body.',
					'code' => 'invalid_controller_result'
				]
			]);
		}

		return $this->responseFactory->from(200, self::HEADERS, $body);
	}

	/**
	 * Отправляет ответ клиенту в формате WordPress Ajax и прерывает выполнение.
	 */
	protected function sendAjaxResponse(ResponseInterface $response): void
	{
		$statusCode = $response->statusCode();
		$headers = $response->headers();
		$body = $response->body();

		// Отправляем HTTP заголовки
		if (function_exists('status_header')) {
			status_header($statusCode);
		}

		foreach ($headers->toArray() as $key => $value) {
			header("{$key}: {$value}");
		}

		if (!$body instanceof KeyValueInterface) {
			echo json_encode([
				'success' => false,
				'data' => [
					'message' => 'incorrect body',
					'code' => 'wp_boundary_error'
				]
			]);
			wp_die('', '', ['response' => $statusCode]);
		}

		// Выводим тело ответа
		echo json_encode($body->toArray());

		// В WordPress Ajax обязательно нужно вызывать wp_die()
		wp_die('', '', ['response' => $statusCode]);
	}

	protected function catch(RequestInterface $request, Throwable $throwable): ResponseInterface
	{
		$accept = $request->headers()?->accept();
		if ($accept !== 'application/json') {
			throw $throwable;
		}

		$statusCode = $throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500;

		$body = [
			'success' => false,
			'data' => [
				'message' => $throwable->getMessage(),
				'code' => $throwable->getCode(),
			]
		];

		$messages = $throwable instanceof HasMessagesInterface ? $throwable->getMessages() : null;
		if ($messages !== null) {
			$body['data']['messages'] = $messages->toArray();
		}

		return $this->responseFactory->from($statusCode, self::HEADERS, $body);
	}
}