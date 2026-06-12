<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

interface MiddlewaresBuilderInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;
	public function withoutMiddlewares(): static;

	public function withMiddleware(MiddlewareInterface $middleware): static;

	public function currentUserCan(Closure $closure): static;
	public function transaction(Closure $closure): static;
	public function verifyNonce(Closure $closure): static;
	public function validate(Closure $closure): static;

	public function build(): array;
}