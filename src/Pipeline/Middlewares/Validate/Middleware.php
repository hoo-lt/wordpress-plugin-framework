<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareException,
	Pipeline\Middlewares\Validate\Validator\ValidatorInterface,
	Pipeline\Middlewares\Validate\Validator\ValidatorFactoryInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ValidatorFactoryInterface $validatorFactory,
		protected array $validators = [],
	) {
	}

	public function validators(): array
	{
		return $this->validators;
	}

	public function withValidators(ValidatorInterface ...$validators): static
	{
		return new static($this->validatorFactory, $validators);
	}

	public function withoutValidators(): static
	{
		return new static($this->validatorFactory, []);
	}

	public function withValidator(ValidatorInterface $validator): static
	{
		return $this->withValidators(...$this->validators, $validator);
	}

	public function body(string $key, Closure $closure): static
	{
		return $this->withValidator(
			$this->validatorFactory->body($key, $closure)
		);
	}

	public function bodyQuery(string $key, Closure $closure): static
	{
		return $this->withValidator(
			$this->validatorFactory->bodyQuery($key, $closure)
		);
	}

	public function query(string $key, Closure $closure): static
	{
		return $this->withValidator(
			$this->validatorFactory->query($key, $closure)
		);
	}

	public function header(string $key, Closure $closure): static
	{
		return $this->withValidator(
			$this->validatorFactory->header($key, $closure)
		);
	}

	public function route(string $key, Closure $closure): static
	{
		return $this->withValidator(
			$this->validatorFactory->route($key, $closure)
		);
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if ($this->validators === []) {
			throw new MiddlewareException('middleware misconfigured');
		}

		$messages = new MessageCollection();

		foreach ($this->validators as $validator) {
			$validator->validate(
				$request,
				$messages->add(...),
			);
		}

		if ($messages->isNotEmpty()) {
			throw new Exceptions\UnprocessableContent\Exception('validation error', '', $messages);
		}

		return $closure($request);
	}
}
