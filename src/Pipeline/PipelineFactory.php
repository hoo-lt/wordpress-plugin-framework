<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Hoo\WordPressPluginFramework\{
	Exceptions\Handler\HandlerInterface,
	Http\Server\Request\RequestFactoryInterface,
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewaresInterface,
};

readonly class PipelineFactory implements PipelineFactoryInterface
{
	public function __construct(
		protected RequestFactoryInterface $requestFactory,
		protected ?MiddlewaresInterface $middlewares = null,
		protected ?HandlerInterface $handler = null,
	) {
	}

	public function middlewares(): ?MiddlewaresInterface
	{
		return $this->middlewares;
	}

	public function withMiddlewares(?MiddlewaresInterface $middlewares): static
	{
		return new static($this->requestFactory, $middlewares, $this->handler);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->requestFactory, null, $this->handler);
	}

	public function handler(): ?HandlerInterface
	{
		return $this->handler;
	}

	public function withHandler(HandlerInterface $handler): static
	{
		return new static($this->requestFactory, $this->middlewares, $handler);
	}

	public function withoutHandler(): static
	{
		return new static($this->requestFactory, $this->middlewares, null);
	}

	public function create(string $method, string $url, array $headers = [], ?string $body = null, ?array $routes = null): PipelineInterface
	{
		return $this->pipeline(
			$this->requestFactory->create($method, $url, $headers, $body, $routes),
		);
	}

	public function createFromServer(): PipelineInterface
	{
		return $this->pipeline(
			$this->requestFactory->createFromServer(),
		);
	}

	protected function pipeline(RequestInterface $request): PipelineInterface
	{
		return new Pipeline($request, $this->middlewares, $this->handler);
	}
}
