<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\ValuesRules;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\ValidateRequest\Values\ValuesInterface,
	Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface,
};

readonly class ValuesRules
{
	public function __construct(
		protected ValuesInterface $values,
		protected array $rules = [],
	) {
	}

	public function values(RequestInterface $request): array
	{
		return ($this->values)($request);
	}

	public function rules(): array
	{
		return $this->rules;
	}
}
