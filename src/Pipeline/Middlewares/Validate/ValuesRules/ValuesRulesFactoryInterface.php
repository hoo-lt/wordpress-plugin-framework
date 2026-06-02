<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Closure;

interface ValuesRulesFactoryInterface
{
	public function body(string $key, Closure $closure): ValuesRulesInterface;
	public function query(string $key, Closure $closure): ValuesRulesInterface;
}
