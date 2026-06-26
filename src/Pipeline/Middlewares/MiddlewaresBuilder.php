<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\CurrentUserCan\Middleware as CurrentUserCanMiddleware,
	Pipeline\Middlewares\CurrentUserCan\Capability\Capability,
	Pipeline\Middlewares\LogExecutionTime\MiddlewareFactoryInterface as LogExecutionTimeMiddlewareFactoryInterface,
	Pipeline\Middlewares\Transaction\MiddlewareFactoryInterface as TransactionMiddlewareFactoryInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorsBuilderInterface,
	Pipeline\Middlewares\Validate\Middleware as ValidateMiddleware,
	Pipeline\Middlewares\VerifyNonce\Middleware as VerifyNonceMiddleware,
};

readonly class MiddlewaresBuilder implements MiddlewaresBuilderInterface
{
	public function __construct(
		protected LogExecutionTimeMiddlewareFactoryInterface $logExecutionTimeMiddlewareFactory,
		protected TransactionMiddlewareFactoryInterface $transactionMiddlewareFactory,
		protected ValidatorsBuilderInterface $validatorsBuilder,
		protected array $middlewares = [],
	) {
	}

	public function middlewares(): array
	{
		return $this->middlewares;
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->logExecutionTimeMiddlewareFactory, $this->transactionMiddlewareFactory, $this->validatorsBuilder, $middlewares);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->logExecutionTimeMiddlewareFactory, $this->transactionMiddlewareFactory, $this->validatorsBuilder, []);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
	}

	public function currentUserCan(Capability $capability): static
	{
		return $this->withMiddleware(
			new CurrentUserCanMiddleware($capability),
		);
	}

	public function logExecutionTime(): static
	{

		return $this->withMiddleware(
			$this->logExecutionTimeMiddlewareFactory->create(),
		);
	}

	public function transaction(): static
	{
		return $this->withMiddleware(
			$this->transactionMiddlewareFactory->create(),
		);
	}

	public function verifyNonce(string $name, string|int $action = -1): static
	{
		return $this->withMiddleware(
			new VerifyNonceMiddleware($name, $action),
		);
	}

	public function validate(Closure $closure): static
	{
		$validatorsBuilder = $closure($this->validatorsBuilder);
		if (!$validatorsBuilder instanceof ValidatorsBuilderInterface) {
			throw new MiddlewaresBuilderException('must return Validators Builder instance');
		}

		return $this->withMiddleware(
			new ValidateMiddleware(
				$validatorsBuilder->build(),
			),
		);
	}

	public function build(): array
	{
		return $this->middlewares;
	}
}