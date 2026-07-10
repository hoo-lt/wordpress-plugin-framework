<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

interface ParameterInterface
{
	public function name(): string;
	public function value(): string;
}
