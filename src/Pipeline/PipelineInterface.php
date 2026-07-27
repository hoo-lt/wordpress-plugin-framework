<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewaresInterface
};

interface PipelineInterface
{
	public function request(): RequestInterface;
	public function withRequest(RequestInterface $request): static;

	public function middlewares(): ?MiddlewaresInterface;
	public function withMiddlewares(?MiddlewaresInterface $middlewares): static;
	public function withoutMiddlewares(): static;

	public function catch(Closure $closure): static;

	public function __invoke(Closure $closure): mixed;
}
