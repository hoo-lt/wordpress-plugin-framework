<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\ValuesRules;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\ValidateRequest\Collections\Rule\Collection as RuleCollection,
	Pipeline\Middlewares\ValidateRequest\Values\ValuesInterface,
	Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface,
};

readonly class ValuesRules
{
	public function __construct(
		protected ValuesInterface $values,
		protected RuleCollection $rules,
	) {
	}

	public function values(RequestInterface $request): array
	{
		return ($this->values)($request);
	}

	public function rules(): RuleCollection
	{
		return $this->rules;
	}

	public function withRules(RuleInterface ...$rules): static
	{
		return new static(
			$this->values,
			$this->rules->with(...$rules),
		);
	}
}
