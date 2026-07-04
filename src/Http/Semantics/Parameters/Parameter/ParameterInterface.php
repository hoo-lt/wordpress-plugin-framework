<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Stringable;

interface ParameterInterface extends Stringable
{
	public function name(): string;
	public function value(): string;
}
