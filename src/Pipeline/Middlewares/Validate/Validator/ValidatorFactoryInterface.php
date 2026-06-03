<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface;

interface ValidatorFactoryInterface
{
	public function create(KeyValueInterface $keyValue, Closure $rulesBuilderClosure): ValidatorInterface;
}
