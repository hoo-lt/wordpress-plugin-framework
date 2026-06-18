<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator\Rules;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValues\KeyValuesInterface;

interface ValidatorFactoryInterface
{
	public function create(KeyValuesInterface $keyValues, Closure $closure): ValidatorInterface;
}
