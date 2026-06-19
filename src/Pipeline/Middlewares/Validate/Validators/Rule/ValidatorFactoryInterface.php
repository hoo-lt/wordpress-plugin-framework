<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

interface ValidatorFactoryInterface
{
	public function create(KeyValueInterface $keyValue, Closure $closure): ValidatorInterface;
}