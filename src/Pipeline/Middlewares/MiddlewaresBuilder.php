<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

readonly class MiddlewaresBuilder
{
	public function __construct(
		protected array $middlewares = []
	) {
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return new static([
			...$this->middlewares,
			$middleware,
		]);
	}

	public function currentUserCan(CurrentUserCan\Capability\Capability $capability): static
	{
		return $this->withMiddleware(
			new CurrentUserCan\Middleware($capability),
		);
	}

	public function verifyNonce(string $name, string|int $action = -1): static
	{
		return $this->withMiddleware(
			new VerifyNonce\Middleware($name, $action),
		);
	}

	public function validateRequest(Closure $closure): static
	{

	}

	public function build(): array
	{
		return $this->middlewares;
	}
}