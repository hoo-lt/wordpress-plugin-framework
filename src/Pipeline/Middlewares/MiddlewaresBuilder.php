<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

readonly class MiddlewaresBuilder implements MiddlewaresBuilderInterface
{
	public function __construct(
		protected Validate\MiddlewareFactoryInterface $validateMiddlewareFactory,
		protected array $middlewares = []
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static(
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
			new CurrentUserCan\Middleware(),
		);
		if (!$currentUserCanMiddleware instanceof CurrentUserCan\MiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return current user can middleware instance');
		}

		return $this->withMiddleware($currentUserCanMiddleware);
	}

	public function verifyNonce(Closure $closure): static
	{
		$verifyNonceMiddleware = $closure(
			new VerifyNonce\Middleware(),
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
		if (!$validateMiddleware instanceof Validate\MiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return validate middleware instance');
		}

		return $this->withMiddleware($validateMiddleware);
	}

	public function build(): array
	{
		return $this->middlewares;
	}
}