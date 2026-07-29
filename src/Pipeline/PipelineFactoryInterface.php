<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Hoo\WordPressPluginFramework\{
	Exceptions\Handler\HandlerInterface,
	Pipeline\Middlewares\MiddlewaresInterface,
};

interface PipelineFactoryInterface
{
	public function middlewares(): ?MiddlewaresInterface;
	public function withMiddlewares(?MiddlewaresInterface $middlewares): static;
	public function withoutMiddlewares(): static;

	public function handler(): ?HandlerInterface;
	public function withHandler(HandlerInterface $handler): static;
	public function withoutHandler(): static;

	public function create(string $method, string $url, array $headers = [], ?string $body = null, ?array $routes = null): PipelineInterface;
	public function createFromServer(): PipelineInterface;
}
