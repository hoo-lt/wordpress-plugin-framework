<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;

interface ValuesRulesInterface
{
	public function values(RequestInterface $request): array;
	public function rules(): array;
}
