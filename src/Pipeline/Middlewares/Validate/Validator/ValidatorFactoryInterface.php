<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface;

interface ValidatorFactoryInterface
{
	public function create(KeyValueInterface $keyValue, Closure $closure): ValidatorInterface;

	public function body(string $key, Closure $closure): ValidatorInterface;
	public function bodyQuery(string $key, Closure $closure): ValidatorInterface;
	public function query(string $key, Closure $closure): ValidatorInterface;
	public function header(string $key, Closure $closure): ValidatorInterface;
	public function route(string $key, Closure $closure): ValidatorInterface;
}
