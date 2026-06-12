<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Handler;

use Hoo\WordPressPluginFramework\{
	Collections\Message\CollectionInterface as MessageCollectionInterface,
	Http\Server\Request\RequestInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	Exceptions\Interfaces\HasStatusCodeInterface,
	Exceptions\Interfaces\HasMessagesInterface,
	View\ViewInterface,
};
use Throwable;

readonly class Handler implements HandlerInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
		protected ViewInterface $view,
	) {
	}

	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface
	{
		$accept = $request->headers()?->accept();
		return match ($accept) {
			'application/json' => $this->json($throwable),
			default => $this->html($throwable),
		};
	}

	protected function html(Throwable $throwable): ResponseInterface
	{
		if (!$this->view->has('exception')) {
			throw $throwable;
		}

		return $this->responseFactory->create(
			$this->statusCode($throwable),
			[
				'Content-Type' => 'text/html',
			],
			$this->view
				->withValues(
					$this->values($throwable)
				)
				->get('exception'),
		);
	}

	protected function json(Throwable $throwable): ResponseInterface
	{
		return $this->responseFactory->create(
			$this->statusCode($throwable),
			[
				'Content-Type' => 'application/json',
			],
			$this->values($throwable),
		);
	}

	protected function values(Throwable $throwable): array
	{
		$values = [
			'message' => $throwable->getMessage(),
			'code' => $throwable->getCode(),
		];

		$messages = $this->messages($throwable);
		if ($messages !== null) {
			if ($messages->isNotEmpty()) {
				$values['messages'] = $messages->all();
			}
		}

		if ($this->isDebug()) {
			$values['file'] = $throwable->getFile();
			$values['line'] = $throwable->getLine();
			$values['trace'] = $throwable->getTrace();
		}

		return $values;
	}

	protected function statusCode(Throwable $throwable): int
	{
		return $throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500;
	}

	protected function messages(Throwable $throwable): ?MessageCollectionInterface
	{
		return $throwable instanceof HasMessagesInterface ? $throwable->getMessages() : null;
	}

	protected function isDebug(): bool
	{
		if (
			defined('WP_DEBUG') &&
			WP_DEBUG
		) {
			return true;
		}

		return false;
	}
}