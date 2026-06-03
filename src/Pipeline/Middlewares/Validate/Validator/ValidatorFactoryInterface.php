<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;

interface ValidatorFactoryInterface
{
	public function body(string $key, Closure $closure): ValidatorInterface;
	public function bodyQuery(string $key, Closure $closure): ValidatorInterface;
	public function query(string $key, Closure $closure): ValidatorInterface;
	public function header(string $key, Closure $closure): ValidatorInterface;
	public function route(string $key, Closure $closure): ValidatorInterface;
}
