<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use Countable;
use IteratorAggregate;
use Stringable;

interface ParametersInterface extends IteratorAggregate, Countable, Stringable
{
	public function parameter(string $name): ?string;
}
