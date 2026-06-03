<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares,
	Pipeline\Middlewares\Validate\Validator\ValidatorInterface,
};

interface MiddlewareInterface extends Middlewares\MiddlewareInterface
{
	public function withValidators(ValidatorInterface ...$validators): static;
	public function withValidator(ValidatorInterface $validator): static;

	public function body(string $key, Closure $closure): static;
	public function bodyQuery(string $key, Closure $closure): static;
	public function query(string $key, Closure $closure): static;
	public function header(string $key, Closure $closure): static;
	public function route(string $key, Closure $closure): static;
}
