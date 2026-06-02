<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values\ValuesInterface;

interface ValuesRulesFactoryInterface
{
	public function create(ValuesInterface $values, Closure $rulesBuilderClosure): ValuesRulesInterface;
}
