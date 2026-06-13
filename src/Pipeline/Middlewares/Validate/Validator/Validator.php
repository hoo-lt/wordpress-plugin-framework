<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValues\KeyValuesInterface,
	Pipeline\Middlewares\Validate\Rules\RuleInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected KeyValuesInterface $keyValues,
		protected array $rules = [],
	) {
	}

	public function withRules(RuleInterface ...$rules): static
	{
		return new static($this->keyValues, $rules);
	}

	public function withoutRules(): static
	{
		return new static($this->keyValues, []);
	}

	public function withRule(RuleInterface $rule): static
	{
		return $this->withRules(...$this->rules, $rule);
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$values = $this->keyValues->values($request);
		if ($values === null) {
			$key = $this->keyValues->key();

			$closure($key, 'no content to validate');
		} else {
			foreach ($values as $key => $value) {
				foreach ($this->rules as $rule) {
					$rule($value, fn($message) => $closure($key, $message));
				}
			}
		}
	}
}
