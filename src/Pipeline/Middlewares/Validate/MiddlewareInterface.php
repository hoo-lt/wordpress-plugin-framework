<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares,
	Pipeline\Middlewares\Validate\Values\ValuesInterface,
};

interface MiddlewareInterface extends Middlewares\MiddlewareInterface
{
	public function withValuesRules(ValuesInterface $values, Closure $closure): static;

	public function body(string $key, Closure $closure): static;
	public function query(string $key, Closure $closure): static;
	public function header(string $key, Closure $closure): static;
	public function route(string $key, Closure $closure): static;
}
