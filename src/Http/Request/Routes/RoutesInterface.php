<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Routes;

use Countable;
use IteratorAggregate;

interface RoutesInterface extends IteratorAggregate, Countable
{
	public function route(string $key): mixed;
	public function withRoute(string $key, mixed $route): static;
	public function withoutRoute(string $key): static;
}
