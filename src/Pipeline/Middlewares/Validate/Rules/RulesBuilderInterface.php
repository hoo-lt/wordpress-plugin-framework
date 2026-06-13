<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules;

use Closure;

interface RulesBuilderInterface
{
	public function rules(): array;
	public function withRules(RuleInterface ...$rules): static;
	public function withoutRules(): static;

	public function withRule(RuleInterface $rule): static;

	public function bool(): static;
	public function closure(Closure $closure): static;
	public function domain(): static;
	public function email(): static;
	public function float(): static;
	public function int(): static;
	public function ip(): static;
	public function mac(): static;
	public function regexp(string $regexp): static;
	public function string(): static;
	public function url(): static;

	public function build(): array;
}