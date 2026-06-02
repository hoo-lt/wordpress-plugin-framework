<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules;

readonly class RulesBuilder implements RulesBuilderInterface
{
	public function __construct(
		protected array $rules = []
	) {
	}

	public function withRules(RuleInterface ...$rules): static
	{
		return new static($rules);
	}

	public function withRule(RuleInterface $rule): static
	{
		return $this->withRules(...$this->rules, $rule);
	}

	public function bool(): static
	{
		return $this->withRule(
			new Bool\Rule(),
		);
	}

	public function float(): static
	{
		return $this->withRule(
			new Float\Rule(),
		);
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