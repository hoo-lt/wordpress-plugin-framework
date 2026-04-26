<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

interface InputInterface
{
	public function key(): string;
	public function values(RequestInterface $request): array;
	public function rules(): array;
	public function withRules(RuleInterface ...$rules): InputInterface;
}
