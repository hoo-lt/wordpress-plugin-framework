<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
	Pipeline\Middlewares\ValidateRequest\Errors\Errors,
};
use Throwable;

readonly class Middleware implements Pipeline\Middlewares\MiddlewareInterface
{
	use Pipeline\Middlewares\MiddlewareTrait;

	public function __construct(
		protected array $validators = [],
		protected ?Pipeline\Middlewares\ValidateRequest\Validators\ValidatorInterface $validator = null,
	) {
	}

	public function withValidator(Pipeline\Middlewares\ValidateRequest\Validators\ValidatorInterface $validator): static
	{
		return new self(
			$this->validators(),
			$validator,
		);
	}

	public function body(string $key): static
	{
		return $this->withValidator(
			new Pipeline\Middlewares\ValidateRequest\Validators\Body\Validator($key),
		);
	}

	public function query(string $key): static
	{
		return $this->withValidator(
			new Pipeline\Middlewares\ValidateRequest\Validators\Query\Validator($key),
		);
	}

	public function withRules(Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface ...$rules): static
	{
		return new self(
			$this->validators,
			$this->validator->withRules(
				...$rules
			)
		);
	}

	public function bool(): static
	{
		return $this->withRules(
			new Pipeline\Middlewares\ValidateRequest\Rules\Bool\Rule(),
		);
	}

	public function float(): static
	{
		return $this->withRules(
			new Pipeline\Middlewares\ValidateRequest\Rules\Float\Rule(),
		);
	}

	public function int(): static
	{
		return $this->withRules(
			new Pipeline\Middlewares\ValidateRequest\Rules\Int\Rule(),
		);
	}

	public function string(): static
	{
		return $this->withRules(
			new Pipeline\Middlewares\ValidateRequest\Rules\String\Rule(),
		);
	}

	public function __invoke(Http\Request\RequestInterface $request, Closure $closure): mixed
	{
		$errors = new Errors();

		foreach ($this->validators() as $validator) {
			$values = $validator->values($request);
			if ($values === []) {
				$errors->add($validator->key(), 'unable to validate');
			}

			foreach ($values as $key => $value) {
				foreach ($validator->rules() as $rule) {
					$rule($value, fn($error) => $errors->add($key, $error));
				}
			}
		}

		if ($errors->any()) {
			throw new MiddlewareException($errors);
		}

		return $closure($request);
	}

	protected function validators(): array
	{
		return $this->validator ? [
			...$this->validators,
			$this->validator,
		] : $this->validators;
	}
}
