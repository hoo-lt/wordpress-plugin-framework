<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Validators;

use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
};

interface ValidatorInterface
{
	public function key(): string;
	public function values(Http\Request\RequestInterface $request): array;

	public function rules(): array;
	public function withRules(Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface ...$rules): static;
}
