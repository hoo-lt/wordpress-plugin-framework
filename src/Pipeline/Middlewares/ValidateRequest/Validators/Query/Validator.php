<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Validators\Query;

use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
};

readonly class Validator implements Pipeline\Middlewares\ValidateRequest\Validators\ValidatorInterface
{
	public function __construct(
		protected string $key,
		protected array $rules = [],
	) {
	}

	public function withRules(Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface ...$rules): static
	{
		return new self(
			$this->key,
			[
				...$this->rules,
				...$rules
			]
		);
	}

	public function validate(Http\Request\RequestInterface $request): void
	{
		$values = $request->url()->query() instanceof Http\KeyValue\KeyValueInterface ? $request->url()->query()->values($this->key) : null;
		if (!$values) {
			throw new Pipeline\Middlewares\ValidateRequest\Validators\ValidatorException();
		}

		foreach ($values as $key => $value) {
			foreach ($this->rules as $rule) {
				if (!$rule($value)) {
					$errors[$key][] = $rule->error();
				}
			}
		}

		if ($errors) {
			throw new Pipeline\Middlewares\ValidateRequest\Validators\ValidatorException();
		}
	}
}
