<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

readonly class MiddlewaresBuilder
{
	public function __construct(
		protected CurrentUserCan\MiddlewareFactoryInterface $currentUserCanMiddlewareFactory,
		protected VerifyNonce\MiddlewareFactoryInterface $verifyNonceMiddlewareFactory,
		protected Validate\MiddlewareFactoryInterface $validateMiddlewareFactory,
		protected array $middlewares = []
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static(
			$this->currentUserCanMiddlewareFactory,
			$this->verifyNonceMiddlewareFactory,
			$this->validateMiddlewareFactory,
			$middlewares
		);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
	}

	public function currentUserCan(Closure $closure): static
	{
		$currentUserCanMiddleware = $closure(
			$this->currentUserCanMiddlewareFactory->create(),
		);
		if (!$currentUserCanMiddleware instanceof CurrentUserCan\MiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return current user can middleware instance');
		}

		return $this->withMiddleware($currentUserCanMiddleware);
	}

	public function verifyNonce(Closure $closure): static
	{
		$verifyNonceMiddleware = $closure(
			$this->verifyNonceMiddlewareFactory->create(),
		);
		if (!$verifyNonceMiddleware instanceof VerifyNonce\MiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return verify nonce middleware instance');
		}

		return $this->withMiddleware($verifyNonceMiddleware);
	}

	public function validate(Closure $closure): static
	{
		$validateMiddleware = $closure(
			$this->validateMiddlewareFactory->create(),
		);
		if (!$validateMiddleware instanceof Validate\Middleware) {
			throw new MiddlewaresBuilderException('must return validate middleware instance');
		}

		return $this->withMiddleware($validateMiddleware);
	}

	public function build(): array
	{
		return $this->middlewares;
	}
}