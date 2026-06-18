<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator\Rules;

use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Validator,
	Pipeline\Middlewares\Validate\Rules\RuleInterface,
};

interface ValidatorInterface extends Validator\ValidatorInterface
{
	public function rules(): array;
	public function withRules(RuleInterface ...$rules): static;
	public function withoutRules(): static;

	public function withRule(RuleInterface $rule): static;
}
