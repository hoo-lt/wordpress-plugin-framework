<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\Values\ValuesInterface,
};

readonly class ValuesRules implements ValuesRulesInterface
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
