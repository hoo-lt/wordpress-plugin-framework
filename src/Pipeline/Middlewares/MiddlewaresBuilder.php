<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\CurrentUserCan\Middleware as CurrentUserCanMiddleware,
	Pipeline\Middlewares\CurrentUserCan\MiddlewareInterface as CurrentUserCanMiddlewareInterface,
	Pipeline\Middlewares\Transaction\MiddlewareFactoryInterface as TransactionMiddlewareFactoryInterface,
	Pipeline\Middlewares\Transaction\MiddlewareInterface as TransactionMiddlewareInterface,
	Pipeline\Middlewares\Validate\MiddlewareFactoryInterface as ValidateMiddlewareFactoryInterface,
	Pipeline\Middlewares\Validate\MiddlewareInterface as ValidateMiddlewareInterface,
	Pipeline\Middlewares\VerifyNonce\Middleware as VerifyNonceMiddleware,
	Pipeline\Middlewares\VerifyNonce\MiddlewareInterface as VerifyNonceMiddlewareInterface,
};

readonly class MiddlewaresBuilder implements MiddlewaresBuilderInterface
{
	public function __construct(
		protected TransactionMiddlewareFactoryInterface $transactionMiddlewareFactory,
		protected ValidateMiddlewareFactoryInterface $validateMiddlewareFactory,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->transactionMiddlewareFactory, $this->validateMiddlewareFactory, $middlewares);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->transactionMiddlewareFactory, $this->validateMiddlewareFactory, []);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
	}

	public function currentUserCan(Closure $closure): static
	{
		$currentUserCanMiddleware = $closure(
			new CurrentUserCanMiddleware(),
		);
		if (!$currentUserCanMiddleware instanceof CurrentUserCanMiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return current user can middleware instance');
		}

		return $this->withMiddleware($currentUserCanMiddleware);
	}

	public function transaction(Closure $closure): static
	{
		$transactionMiddleware = $closure(
			$this->transactionMiddlewareFactory->create(),
		);
		if (!$transactionMiddleware instanceof TransactionMiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return transaction middleware instance');
		}

		return $this->withMiddleware($transactionMiddleware);
	}

	public function verifyNonce(Closure $closure): static
	{
		$verifyNonceMiddleware = $closure(
			new VerifyNonceMiddleware(),
		);
		if (!$verifyNonceMiddleware instanceof VerifyNonceMiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return verify nonce middleware instance');
		}

		return $this->withMiddleware($verifyNonceMiddleware);
	}

	public function validate(Closure $closure): static
	{
		$validateMiddleware = $closure(
			$this->validateMiddlewareFactory->create(),
		);
		if (!$validateMiddleware instanceof ValidateMiddlewareInterface) {
			throw new MiddlewaresBuilderException('must return validate middleware instance');
		}

		return $this->withMiddleware($validateMiddleware);
	}

	public function build(): array
	{
		return $this->middlewares;
	}
}