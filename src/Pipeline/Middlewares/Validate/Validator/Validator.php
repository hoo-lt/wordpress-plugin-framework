<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Rules\RuleInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected KeyValueInterface $keyValue,
		protected array $rules = [],
	) {
	}

	public function withRules(RuleInterface ...$rules): static
	{
		return new static($this->keyValue, $rules);
	}

	public function withoutRules(): static
	{
		return new static($this->keyValue, []);
	}

	public function withRule(RuleInterface $rule): static
	{
		return $this->withRules(...$this->rules, $rule);
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$values = $this->keyValue->values($request);
		if ($values === null) {
			$key = $this->keyValue->key();

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
