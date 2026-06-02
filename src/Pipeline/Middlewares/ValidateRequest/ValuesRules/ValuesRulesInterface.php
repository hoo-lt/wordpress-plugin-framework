<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\ValuesRules;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface,
};

interface ValuesRulesInterface
{
	public function values(RequestInterface $request): array;

	public function rules(): array;
	public function withRules(RuleInterface ...$rules): static;
}
