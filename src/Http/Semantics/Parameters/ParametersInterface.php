<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use Countable;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterInterface;
use IteratorAggregate;

interface ParametersInterface extends IteratorAggregate, Countable
{
	public function parameter(string $name): ?string;
}
