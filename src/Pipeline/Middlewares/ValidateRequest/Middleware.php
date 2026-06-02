<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\ValuesRulesInterface,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\ValuesRules as ValuesRules,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\Body\ValuesRules as BodyValuesRules,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\Query\ValuesRules as QueryValuesRules,
	Pipeline\Middlewares\ValidateRequest\Rules\Bool\Rule as BoolRule,
	Pipeline\Middlewares\ValidateRequest\Rules\Float\Rule as FloatRule,
	Pipeline\Middlewares\ValidateRequest\Rules\Int\Rule as IntRule,
	Pipeline\Middlewares\ValidateRequest\Rules\String\Rule as StringRule,
	Pipeline\Middlewares\ValidateRequest\ValuesRules\ValuesRulesFactory
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ValuesRulesFactory $valuesRulesFactory,
		protected array $valuesRules = [],
	) {
	}

	public function body(string $key, Closure $closure): static
	{
		return new static(
			$this->valuesRulesFactory,
			[
				...$this->valuesRules,
				$this->valuesRulesFactory->body($key, $closure),
			]
		);
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$messages = new MessageCollection();

		foreach ($this->valuesRules as $valuesRules) {
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
			throw new Http\Exceptions\UnprocessableContent\Exception('validation error', '');
		}

		return $closure($request);
	}
}
