<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules;

readonly class RulesBuilder
{
	public function __construct(
		protected array $rules = []
	) {
	}

	public function withRule(RuleInterface $rule): static
	{
		return new static([
			...$this->rules,
			$rule,
		]);
	}

	public function int(): static
	{
		return $this->withRule(
			new Int\Rule(),
		);
	}

	public function string(): static
	{
		return $this->withRule(
			new String\Rule(),
		);
	}

	public function build(): array
	{
		return $this->rules;
	}
}