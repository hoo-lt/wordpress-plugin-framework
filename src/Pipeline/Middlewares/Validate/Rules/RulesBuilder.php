<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\Bool\Rule as BoolRule,
	Pipeline\Middlewares\Validate\Rules\Closure\Rule as ClosureRule,
	Pipeline\Middlewares\Validate\Rules\Domain\Rule as DomainRule,
	Pipeline\Middlewares\Validate\Rules\Email\Rule as EmailRule,
	Pipeline\Middlewares\Validate\Rules\Float\Rule as FloatRule,
	Pipeline\Middlewares\Validate\Rules\Int\Rule as IntRule,
	Pipeline\Middlewares\Validate\Rules\Ip\Rule as IpRule,
	Pipeline\Middlewares\Validate\Rules\Mac\Rule as MacRule,
	Pipeline\Middlewares\Validate\Rules\Regexp\Rule as RegexpRule,
	Pipeline\Middlewares\Validate\Rules\String\Rule as StringRule,
	Pipeline\Middlewares\Validate\Rules\Url\Rule as UrlRule,
};

readonly class RulesBuilder implements RulesBuilderInterface
{
	public function __construct(
		protected array $rules = [],
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
			new BoolRule(),
		);
	}

	public function closure(Closure $closure): static
	{
		return $this->withRule(
			new ClosureRule($closure),
		);
	}

	public function domain(): static
	{
		return $this->withRule(
			new DomainRule(),
		);
	}

	public function email(): static
	{
		return $this->withRule(
			new EmailRule(),
		);
	}

	public function float(): static
	{
		return $this->withRule(
			new FloatRule(),
		);
	}

	public function int(): static
	{
		return $this->withRule(
			new IntRule(),
		);
	}

	public function ip(): static
	{
		return $this->withRule(
			new IpRule(),
		);
	}

	public function mac(): static
	{
		return $this->withRule(
			new MacRule(),
		);
	}

	public function regexp(string $regexp): static
	{
		return $this->withRule(
			new RegexpRule($regexp),
		);
	}

	public function string(): static
	{
		return $this->withRule(
			new StringRule(),
		);
	}

	public function url(): static
	{
		return $this->withRule(
			new UrlRule(),
		);
	}

	public function build(): array
	{
		return $this->rules;
	}
}