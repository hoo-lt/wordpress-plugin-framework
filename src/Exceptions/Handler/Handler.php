<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Handler;

use Hoo\WordPressPluginFramework\{
	Exceptions\Handler\ViewModels\ViewModel,
	Exceptions\Interfaces\HasStatusCodeInterface,
	Http\Coders\CoderFactoryInterface,
	Http\Server\Request\RequestInterface,
	Http\Server\Response\ResponseInterface,
	Http\Server\Response\ResponseFactoryInterface,
	View\Model\ModelInterface as ViewModelInterface,
	View\ViewFactoryInterface,
};
use Throwable;

readonly class Handler implements HandlerInterface
{
	public function __construct(
		protected CoderFactoryInterface $coderFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected ViewFactoryInterface $viewFactory,
	) {
	}

	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface
	{
		$viewModel = ViewModel::createFromThrowable($throwable);

		$contentType = $this->contentType($request);
		if ($contentType === 'text/html') {
			$view = $this->viewFactory->tryCreate('exception', $viewModel);
			if ($view === null) {
				throw $throwable;
			}

			$body = $view->render();
		}

		$body = $viewModel->toArray();

		return $this->responseFactory->create(
			$this->statusCode($throwable),
			[
				'Content-Type' => $contentType,
			],
			$body,
		);
	}

	protected function statusCode(Throwable $throwable): int
	{
		return $throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500;
	}

	protected function contentType(RequestInterface $request): string
	{
		$accept = $request->headers()?->accept();
		if ($accept === null) {
			return 'text/html';
		}

		if ($this->coderFactory->tryCreateEncoder($accept, []) === null) {
			return 'text/html';
		}

		return $accept;
	}
}