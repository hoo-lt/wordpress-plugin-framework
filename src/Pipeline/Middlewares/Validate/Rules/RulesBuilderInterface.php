<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules;

interface RulesBuilderInterface
{
	public function withRules(RuleInterface ...$rules): static;
	public function withRule(RuleInterface $rule): static;

	public function bool(): static;
	public function float(): static;
	public function int(): static;
	public function string(): static;

	public function build(): array;
}