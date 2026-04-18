<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator\Fields;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

readonly class Field implements FieldInterface
{
	public function __construct(
		private string $name,
		private Method $method,
		private array $rules = [],
	) {
	}

	public function name(): string
	{
		return $this->name;
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function rules(): array
	{
		return $this->rules;
	}

	public function withRule(RuleInterface $rule): FieldInterface
	{
		return new self($this->name, $this->method, [...$this->rules, $rule]);
	}
}
