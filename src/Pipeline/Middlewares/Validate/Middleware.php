<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareException,
	Pipeline\Middlewares\Validate\Validator\ValidatorInterface,
	Pipeline\Middlewares\Validate\Validator\ValidatorFactoryInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\KeyValue\Body\KeyValue as BodyKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\Query\KeyValue as QueryKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\Header\KeyValue as HeaderKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\Route\KeyValue as RouteKeyValue,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ValidatorFactoryInterface $validatorFactory,
		protected array $validators = [],
	) {
	}

	public function withValidators(ValidatorInterface ...$validators): static
	{
		return new static($this->validatorFactory, $validators);
	}

	public function withValidator(ValidatorInterface $validator): static
	{
		return $this->withValidators(...$this->validators, $validator);
	}

	public function withFactoryCreatedValidator(KeyValueInterface $keyValue, Closure $rulesBuilderClosure): static
	{
		return $this->withValidator(
			$this->validatorFactory->create($keyValue, $rulesBuilderClosure),
		);
	}

	public function body(string $key, Closure $rulesBuilderClosure): static
	{
		return $this->withFactoryCreatedValidator(
			new BodyKeyValue($key),
			$rulesBuilderClosure,
		);
	}

	public function query(string $key, Closure $rulesBuilderClosure): static
	{
		return $this->withFactoryCreatedValidator(
			new QueryKeyValue($key),
			$rulesBuilderClosure,
		);
	}

	public function header(string $key, Closure $rulesBuilderClosure): static
	{
		return $this->withFactoryCreatedValidator(
			new HeaderKeyValue($key),
			$rulesBuilderClosure,
		);
	}

	public function route(string $key, Closure $rulesBuilderClosure): static
	{
		return $this->withFactoryCreatedValidator(
			new RouteKeyValue($key),
			$rulesBuilderClosure,
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
