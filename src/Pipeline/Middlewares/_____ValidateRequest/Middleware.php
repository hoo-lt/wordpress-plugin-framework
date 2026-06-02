<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\ValuesRulesInterface,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\Body\ValuesRules as BodyValuesRules,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\Query\ValuesRules as QueryValuesRules,
	Pipeline\Middlewares\ValidateRequest\Rules\Bool\Rule as BoolRule,
	Pipeline\Middlewares\ValidateRequest\Rules\Float\Rule as FloatRule,
	Pipeline\Middlewares\ValidateRequest\Rules\Int\Rule as IntRule,
	Pipeline\Middlewares\ValidateRequest\Rules\String\Rule as StringRule
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ?self $self = null,
		protected ?ValuesRulesInterface $valuesRules = null,
	) {
	}

	public function withValuesRules(ValuesRulesInterface $valuesRules): static
	{
		return new static(
			$this,
			$valuesRules
		);
	}

	public function body(string $key): static
	{
		return $this->withValuesRules(
			new BodyValuesRules($key),
		);
	}

	public function query(string $key): static
	{
		return $this->withValuesRules(
			new QueryValuesRules($key),
		);
	}

	public function withRules(RuleInterface ...$rules): static
	{
		if ($this->valuesRules === null) {
			//throw
		}

		return new static(
			$this->self,
			$this->valuesRules->withRules(...$rules),
		);
	}

	public function bool(): static
	{
		return $this->withRules(
			new BoolRule(),
		);
	}

	public function float(): static
	{
		return $this->withRules(
			new FloatRule(),
		);
	}

	public function int(): static
	{
		return $this->withRules(
			new IntRule(),
		);
	}

	public function string(): static
	{
		return $this->withRules(
			new StringRule(),
		);
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$messages = new MessageCollection();

		$valuesRules = $this->valuesRules();
		foreach ($valuesRules as $valuesRules) {
			$values = $valuesRules->values($request);
			if ($values === []) {
				throw new Http\Exceptions\BadRequest\Exception('incorrect request', '');
			}

			$rules = $valuesRules->rules();

			foreach ($values as $key => $value) {
				foreach ($rules as $rule) {
					$rule($value, fn($message) => $messages->add($key, $message));
				}
			}
		}

		if ($messages->isNotEmpty()) {
			throw new Http\Exceptions\UnprocessableContent\Exception(
				'validation error',
				'',
			);
		}

		return $closure($request);
	}

	protected function valuesRules(): array
	{
		$valuesRules = $this->self ? $this->self->valuesRules() : [];

		if ($this->valuesRules !== null) {
			$valuesRules[] = $this->valuesRules;
		}

		return $valuesRules;
	}
}
